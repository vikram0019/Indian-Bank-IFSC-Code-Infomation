'use client';

import { useState } from 'react';

export default function AdminLoginForm({ onSubmit, error }) {
  const [secret, setSecret] = useState('');

  function handleSubmit(e) {
    e.preventDefault();
    onSubmit(secret);
  }

  return (
    <form onSubmit={handleSubmit} className="max-w-sm mx-auto mt-16 space-y-3">
      <h1 className="text-xl font-semibold text-center">Admin Login</h1>
      <input
        type="password"
        value={secret}
        onChange={(e) => setSecret(e.target.value)}
        placeholder="Admin secret"
        className="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 bg-white dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
      {error && <p className="text-sm text-red-600 dark:text-red-400">{error}</p>}
      <button type="submit" className="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
        Log in
      </button>
    </form>
  );
}
