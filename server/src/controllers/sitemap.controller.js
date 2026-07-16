const Branch = require('../models/Branch');

const PAGE_SIZE = 5000;

async function sitemapData(req, res, next) {
  try {
    const cursor = parseInt(req.query.cursor, 10) || 0;
    const limit = Math.min(parseInt(req.query.limit, 10) || PAGE_SIZE, PAGE_SIZE);

    const [ifscRows, total] = await Promise.all([
      Branch.find({}).select('IFSC BANKCODE CITY').sort({ IFSC: 1 }).skip(cursor).limit(limit).lean(),
      Branch.estimatedDocumentCount(),
    ]);

    const nextCursor = cursor + ifscRows.length < total ? cursor + ifscRows.length : null;

    res.json({
      total,
      nextCursor,
      items: ifscRows.map((r) => ({ ifsc: r.IFSC, bankcode: r.BANKCODE, city: r.CITY })),
    });
  } catch (err) {
    next(err);
  }
}

module.exports = { sitemapData };
