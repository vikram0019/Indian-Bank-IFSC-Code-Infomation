const Branch = require('../models/Branch');
const escapeRegex = require('../utils/escapeRegex');

function paginationParams(req) {
  const page = Math.max(parseInt(req.query.page, 10) || 1, 1);
  const limit = Math.min(Math.max(parseInt(req.query.limit, 10) || 20, 1), 100);
  return { page, limit, skip: (page - 1) * limit };
}

async function searchByBankAndCity(req, res, next) {
  try {
    const { bank, city } = req.query;
    if (!bank || !city) {
      return res.status(400).json({ error: 'Both bank and city query params are required' });
    }
    const { page, limit, skip } = paginationParams(req);

    // Match by bank NAME prefix, not BANKCODE: many IFSC codes carry another
    // bank's routing prefix (e.g. small banks' IMPS-only codes start with
    // "HDFC" because they settle via HDFC's rails), so BANKCODE alone would
    // incorrectly pull in unrelated banks that happen to share a code prefix.
    const filter = {
      BANK: new RegExp(`^${escapeRegex(bank)}`, 'i'),
      CITY: new RegExp(`^${escapeRegex(city)}$`, 'i'),
    };

    const [results, total] = await Promise.all([
      Branch.find(filter).sort({ BRANCH: 1 }).skip(skip).limit(limit).lean(),
      Branch.countDocuments(filter),
    ]);

    res.json({ page, limit, total, results });
  } catch (err) {
    next(err);
  }
}

async function citiesForBank(req, res, next) {
  try {
    const { bank } = req.query;
    if (!bank) {
      return res.status(400).json({ error: 'bank query param is required' });
    }

    const bankRegex = new RegExp(`^${escapeRegex(bank)}`, 'i');

    const [cities, sample] = await Promise.all([
      Branch.aggregate([
        { $match: { BANK: bankRegex } },
        { $group: { _id: { city: '$CITY', state: '$STATE' }, count: { $sum: 1 } } },
        { $sort: { '_id.city': 1 } },
        { $limit: 500 },
      ]),
      Branch.findOne({ BANK: bankRegex }).select('BANK').lean(),
    ]);

    res.json({
      bank: sample?.BANK || bank,
      cities: cities.map((c) => ({ city: c._id.city, state: c._id.state, count: c.count })),
    });
  } catch (err) {
    next(err);
  }
}

async function searchByBranchName(req, res, next) {
  try {
    const { name } = req.query;
    if (!name) {
      return res.status(400).json({ error: 'name query param is required' });
    }
    const { page, limit, skip } = paginationParams(req);

    const filter = { BRANCH: new RegExp(escapeRegex(name), 'i') };

    const [results, total] = await Promise.all([
      Branch.find(filter).sort({ BRANCH: 1 }).skip(skip).limit(limit).lean(),
      Branch.countDocuments(filter),
    ]);

    res.json({ page, limit, total, results });
  } catch (err) {
    next(err);
  }
}

module.exports = { searchByBankAndCity, searchByBranchName, citiesForBank };
