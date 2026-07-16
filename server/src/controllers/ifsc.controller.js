const Branch = require('../models/Branch');

async function getByIfsc(req, res, next) {
  try {
    const code = req.params.code.toUpperCase();
    const branch = await Branch.findOne({ IFSC: code }).lean();

    if (!branch) {
      return res.status(404).json({ error: `No branch found for IFSC code ${code}` });
    }

    res.json(branch);
  } catch (err) {
    next(err);
  }
}

module.exports = { getByIfsc };
