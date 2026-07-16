const mongoose = require('mongoose');

const branchSchema = new mongoose.Schema(
  {
    IFSC: { type: String, required: true, unique: true, uppercase: true, trim: true },
    BANK: { type: String, index: true },
    BANKCODE: { type: String, index: true },
    BRANCH: { type: String, index: true },
    CENTRE: String,
    DISTRICT: String,
    STATE: String,
    ADDRESS: String,
    CITY: { type: String, index: true },
    CONTACT: String,
    IMPS: Boolean,
    RTGS: Boolean,
    ISO3166: String,
    NEFT: Boolean,
    MICR: String,
    UPI: Boolean,
    SWIFT: String,
  },
  { timestamps: true }
);

branchSchema.index({ BANKCODE: 1, CITY: 1 });

module.exports = mongoose.model('Branch', branchSchema);
