'use client';

import { useState } from 'react';
import AdminLoginForm from '@/components/AdminLoginForm';
import { adminStats, adminRefresh } from '@/lib/api';

function formatDate(iso) {
  return iso ? new Date(iso).toLocaleString() : 'Never';
}

export default function AdminPage() {
  const [secret, setSecret] = useState(null);
  const [loginError, setLoginError] = useState('');
  const [stats, setStats] = useState(null);
  const [refreshing, setRefreshing] = useState(false);
  const [refreshMessage, setRefreshMessage] = useState('');

  async function handleLogin(candidateSecret) {
    try {
      const data = await adminStats(candidateSecret);
      setStats(data);
      setSecret(candidateSecret);
      setLoginError('');
    } catch (err) {
      setLoginError(err.message);
    }
  }

  async function handleRefresh() {
    setRefreshing(true);
    setRefreshMessage('');
    try {
      const summary = await adminRefresh(secret, 500);
      setRefreshMessage(
        `Refresh complete: ${summary.addedCount} added, ${summary.modifiedCount} modified, ${summary.removedCount} removed.`
      );
      const data = await adminStats(secret);
      setStats(data);
    } catch (err) {
      setRefreshMessage(`Error: ${err.message}`);
    } finally {
      setRefreshing(false);
    }
  }

  if (!secret) {
    return <AdminLoginForm onSubmit={handleLogin} error={loginError} />;
  }

  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold mb-6">Admin Dashboard</h1>

      <div className="grid grid-cols-2 gap-4 mb-8">
        <div className="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
          <p className="text-sm text-gray-500">Total Branches</p>
          <p className="text-2xl font-bold">{stats.totalBranches?.toLocaleString()}</p>
        </div>
        <div className="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
          <p className="text-sm text-gray-500">Last Refresh</p>
          <p className="text-sm font-medium">{formatDate(stats.lastRun?.runAt)}</p>
        </div>
      </div>

      <button
        type="button"
        onClick={handleRefresh}
        disabled={refreshing}
        className="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium disabled:opacity-40 mb-2"
      >
        {refreshing ? 'Refreshing...' : 'Refresh Now (sample: 500 rows)'}
      </button>
      {refreshMessage && <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">{refreshMessage}</p>}

      <h2 className="font-semibold mb-2">Recent Change Log</h2>
      <div className="space-y-2">
        {stats.recentChangeLogs?.map((log) => (
          <div key={log._id} className="border border-gray-200 dark:border-gray-800 rounded-lg p-3 text-sm">
            <div className="flex justify-between">
              <span>{formatDate(log.runAt)}</span>
              <span
                className={
                  log.status === 'success'
                    ? 'text-green-700 dark:text-green-400 font-medium'
                    : 'text-red-600 dark:text-red-400 font-medium'
                }
              >
                {log.status}
              </span>
            </div>
            <p className="text-gray-500 mt-1">
              +{log.addedCount} / ~{log.modifiedCount} / -{log.removedCount} — total {log.totalRecordsAfter}
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}
