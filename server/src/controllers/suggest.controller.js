const Branch = require('../models/Branch');
const escapeRegex = require('../utils/escapeRegex');

const SUGGEST_LIMIT = 10;

async function suggest(req, res, next) {
  try {
    const q = (req.query.q || '').trim();
    const type = req.query.type;

    if (!q) {
      return res.json([]);
    }

    const prefix = new RegExp(`^${escapeRegex(q)}`, 'i');
    const tasks = [];

    if (!type || type === 'ifsc') {
      tasks.push(
        Branch.find({ IFSC: new RegExp(`^${escapeRegex(q.toUpperCase())}`) })
          .limit(SUGGEST_LIMIT)
          .select('IFSC BANK BRANCH')
          .lean()
          .then((rows) =>
            rows.map((r) => ({ type: 'ifsc', label: `${r.IFSC} — ${r.BANK}, ${r.BRANCH}`, value: r.IFSC }))
          )
      );
    }

    if (!type || type === 'bank') {
      tasks.push(
        Branch.find({ BANK: prefix })
          .limit(SUGGEST_LIMIT * 3)
          .select('BANK')
          .lean()
          .then((rows) => {
            const seen = new Set();
            return rows
              .filter((r) => {
                if (!r.BANK || seen.has(r.BANK)) return false;
                seen.add(r.BANK);
                return true;
              })
              .slice(0, SUGGEST_LIMIT)
              // value is the bank NAME, not BANKCODE: BANKCODE is derived from the
              // IFSC prefix and isn't a reliable bank identifier (see search.controller.js).
              .map((r) => ({ type: 'bank', label: r.BANK, value: r.BANK }));
          })
      );
    }

    if (!type || type === 'branch') {
      tasks.push(
        Branch.find({ BRANCH: prefix })
          .limit(SUGGEST_LIMIT)
          .select('IFSC BANK BRANCH')
          .lean()
          .then((rows) =>
            rows.map((r) => ({ type: 'branch', label: `${r.BRANCH} (${r.BANK})`, value: r.IFSC }))
          )
      );
    }

    if (type === 'city') {
      tasks.push(
        Branch.find({ CITY: prefix })
          .limit(SUGGEST_LIMIT * 5)
          .select('CITY STATE')
          .lean()
          .then((rows) => {
            const seen = new Set();
            return rows
              .filter((r) => {
                if (!r.CITY || seen.has(r.CITY)) return false;
                seen.add(r.CITY);
                return true;
              })
              .slice(0, SUGGEST_LIMIT)
              .map((r) => ({ type: 'city', label: `${r.CITY}, ${r.STATE}`, value: r.CITY }));
          })
      );
    }

    const results = (await Promise.all(tasks)).flat().slice(0, SUGGEST_LIMIT);
    res.json(results);
  } catch (err) {
    next(err);
  }
}

module.exports = { suggest };
