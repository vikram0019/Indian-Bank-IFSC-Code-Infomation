'use client';

import { useMemo, useState } from 'react';
import Link from 'next/link';

function slugify(value) {
  return value.trim().toLowerCase().replace(/\s+/g, '-');
}

export default function BankCityFilter({ bankSlug, cities }) {
  const [query, setQuery] = useState('');

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return cities;
    return cities.filter(
      (c) => c.city.toLowerCase().includes(q) || c.state.toLowerCase().includes(q)
    );
  }, [query, cities]);

  return (
    <div>
      <input
        type="text"
        value={query}
        onChange={(e) => setQuery(e.target.value)}
        placeholder="Search city or town…"
        className="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 mb-4 bg-white dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400"
        autoComplete="off"
      />

      {filtered.length === 0 ? (
        <p className="text-sm text-gray-500">No matching city or town found.</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
          {filtered.map((c) => (
            <Link
              key={`${c.city}-${c.state}`}
              href={`/bank/${bankSlug}/${slugify(c.city)}`}
              className="flex justify-between items-center border border-gray-200 dark:border-gray-800 rounded-lg px-4 py-2 text-sm hover:border-blue-400"
            >
              <span>
                {c.city}
                <span className="text-gray-500">, {c.state}</span>
              </span>
              <span className="text-gray-400">{c.count}</span>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}
