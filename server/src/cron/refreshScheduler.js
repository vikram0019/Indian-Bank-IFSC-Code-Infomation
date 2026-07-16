const cron = require('node-cron');
const { cronEnabled } = require('../config/env');
const { runRefresh } = require('../../scripts/refreshData');

// node-cron has no native "every 15 days" unit; this approximates it by running
// at 03:00 on day-of-month steps of 15. Real production scheduling should use
// Linux crontab on the deployment VPS instead of an in-process timer.
const SCHEDULE = '0 3 */15 * *';

function startRefreshScheduler() {
  if (!cronEnabled) {
    console.log('Cron refresh disabled (CRON_ENABLED=false)');
    return;
  }

  cron.schedule(SCHEDULE, async () => {
    console.log('Scheduled IFSC data refresh starting...');
    try {
      const summary = await runRefresh();
      console.log('Scheduled refresh complete:', summary);
    } catch (err) {
      console.error('Scheduled refresh failed:', err);
    }
  });

  console.log(`IFSC refresh scheduled: "${SCHEDULE}"`);
}

module.exports = startRefreshScheduler;
