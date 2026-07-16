const app = require('./app');
const connectDB = require('./db/connect');
const startRefreshScheduler = require('./cron/refreshScheduler');
const { port } = require('./config/env');

(async () => {
  await connectDB();
  startRefreshScheduler();

  app.listen(port, () => {
    console.log(`IFSC Finder API listening on port ${port}`);
  });
})().catch((err) => {
  console.error('Failed to start server:', err);
  process.exit(1);
});
