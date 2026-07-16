require('dotenv').config();

module.exports = {
  port: process.env.PORT || 4000,
  mongodbUri: process.env.MONGODB_URI || 'mongodb://localhost:27017/ifsc_finder',
  adminSecret: process.env.ADMIN_SECRET || 'changeme',
  ifscSourceUrl:
    process.env.IFSC_SOURCE_URL ||
    'https://github.com/razorpay/ifsc/releases/latest/download/IFSC.csv',
  cronEnabled: process.env.CRON_ENABLED !== 'false',
  clientOrigin: process.env.CLIENT_ORIGIN || 'http://localhost:3000',
};
