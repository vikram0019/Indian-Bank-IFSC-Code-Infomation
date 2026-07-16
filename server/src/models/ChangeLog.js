const mongoose = require('mongoose');

const changeLogSchema = new mongoose.Schema({
  runAt: { type: Date, default: Date.now },
  added: [String],
  removed: [String],
  addedCount: { type: Number, default: 0 },
  removedCount: { type: Number, default: 0 },
  modifiedCount: { type: Number, default: 0 },
  totalRecordsAfter: { type: Number, default: 0 },
  sourceUrl: String,
  durationMs: Number,
  status: { type: String, enum: ['success', 'failed'], default: 'success' },
  error: String,
});

module.exports = mongoose.model('ChangeLog', changeLogSchema);
