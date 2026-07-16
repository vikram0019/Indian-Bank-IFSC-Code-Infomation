require('dotenv').config();
const { parse } = require('csv-parse');
const { Readable } = require('node:stream');
const Branch = require('../src/models/Branch');
const ChangeLog = require('../src/models/ChangeLog');
const { ifscSourceUrl } = require('../src/config/env');

const BATCH_SIZE = 1000;

function toBool(val) {
  return typeof val === 'string' && /^(true|1|yes)$/i.test(val.trim());
}

function rowToBranch(row) {
  const ifsc = row.IFSC.toUpperCase().trim();
  return {
    IFSC: ifsc,
    BANK: row.BANK,
    // The source CSV has no BANKCODE column; IFSC's first 4 characters are the bank code by spec.
    BANKCODE: row.BANKCODE || ifsc.slice(0, 4),
    BRANCH: row.BRANCH,
    CENTRE: row.CENTRE,
    DISTRICT: row.DISTRICT,
    STATE: row.STATE,
    ADDRESS: row.ADDRESS,
    CITY: row.CITY,
    CONTACT: row.CONTACT,
    IMPS: toBool(row.IMPS),
    RTGS: toBool(row.RTGS),
    ISO3166: row.ISO3166,
    NEFT: toBool(row.NEFT),
    MICR: row.MICR,
    UPI: toBool(row.UPI),
    SWIFT: row.SWIFT || null,
  };
}

async function runRefresh({ limit = null, sourceUrl = null } = {}) {
  const start = Date.now();
  const source = sourceUrl || ifscSourceUrl;

  try {
    const beforeCodes = limit ? null : new Set(await Branch.distinct('IFSC'));

    const response = await fetch(source);
    if (!response.ok || !response.body) {
      throw new Error(`Failed to fetch IFSC source: ${response.status} ${response.statusText}`);
    }

    const parser = Readable.fromWeb(response.body).pipe(
      parse({ columns: true, skip_empty_lines: true, trim: true })
    );

    let batch = [];
    let rowCount = 0;
    let upsertedCount = 0;
    let modifiedCount = 0;

    const flush = async () => {
      if (batch.length === 0) return;
      const ops = batch.map((doc) => ({
        updateOne: {
          filter: { IFSC: doc.IFSC },
          update: { $set: doc },
          upsert: true,
        },
      }));
      const result = await Branch.bulkWrite(ops, { ordered: false });
      upsertedCount += result.upsertedCount || 0;
      modifiedCount += result.modifiedCount || 0;
      batch = [];
    };

    for await (const row of parser) {
      if (!row.IFSC) continue;
      batch.push(rowToBranch(row));
      rowCount++;

      if (batch.length >= BATCH_SIZE) {
        await flush();
      }
      if (limit && rowCount >= limit) {
        parser.destroy();
        break;
      }
    }
    await flush();

    let added = [];
    let removed = [];
    if (!limit && beforeCodes) {
      const afterCodes = new Set(await Branch.distinct('IFSC'));
      added = [...afterCodes].filter((c) => !beforeCodes.has(c));
      removed = [...beforeCodes].filter((c) => !afterCodes.has(c));
      if (removed.length > 0) {
        await Branch.deleteMany({ IFSC: { $in: removed } });
      }
    }

    const totalRecordsAfter = await Branch.estimatedDocumentCount();
    const durationMs = Date.now() - start;

    const changeLog = await ChangeLog.create({
      added,
      removed,
      addedCount: upsertedCount,
      removedCount: removed.length,
      modifiedCount,
      totalRecordsAfter,
      sourceUrl: source,
      durationMs,
      status: 'success',
    });

    return {
      rowsProcessed: rowCount,
      addedCount: upsertedCount,
      removedCount: removed.length,
      modifiedCount,
      totalRecordsAfter,
      durationMs,
      changeLogId: changeLog._id,
    };
  } catch (err) {
    await ChangeLog.create({
      sourceUrl: source,
      durationMs: Date.now() - start,
      status: 'failed',
      error: err.message,
    });
    throw err;
  }
}

function parseArgs(argv) {
  const args = { limit: null, source: null };
  for (let i = 0; i < argv.length; i++) {
    if (argv[i] === '--limit') args.limit = parseInt(argv[++i], 10);
    if (argv[i] === '--source') args.source = argv[++i];
  }
  return args;
}

if (require.main === module) {
  const connectDB = require('../src/db/connect');
  const args = parseArgs(process.argv.slice(2));

  (async () => {
    await connectDB();
    console.log(`Starting IFSC refresh${args.limit ? ` (limit: ${args.limit})` : ''}...`);
    const summary = await runRefresh({ limit: args.limit, sourceUrl: args.source });
    console.log('Refresh complete:', summary);
    process.exit(0);
  })().catch((err) => {
    console.error('Refresh failed:', err);
    process.exit(1);
  });
}

module.exports = { runRefresh };
