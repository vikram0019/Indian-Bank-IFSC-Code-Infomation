const Branch = require('../models/Branch');
const ChangeLog = require('../models/ChangeLog');
const { runRefresh } = require('../../scripts/refreshData');

async function refresh(req, res, next) {
  try {
    const limit = req.body?.limit ? parseInt(req.body.limit, 10) : null;
    const summary = await runRefresh({ limit });
    res.json(summary);
  } catch (err) {
    next(err);
  }
}

async function stats(req, res, next) {
  try {
    const [totalBranches, lastRun, recentChangeLogs] = await Promise.all([
      Branch.estimatedDocumentCount(),
      ChangeLog.findOne().sort({ runAt: -1 }).lean(),
      ChangeLog.find().sort({ runAt: -1 }).limit(10).lean(),
    ]);

    res.json({ totalBranches, lastRun, recentChangeLogs });
  } catch (err) {
    next(err);
  }
}

module.exports = { refresh, stats };
