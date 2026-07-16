'use client';

import { useEffect, useRef, useState } from 'react';
import { suggest } from '@/lib/api';

export default function SearchBar({ value, onChange, suggestType, onSelectSuggestion, placeholder }) {
  const [suggestions, setSuggestions] = useState([]);
  const [open, setOpen] = useState(false);
  const debounceRef = useRef(null);

  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current);

    if (!value || value.trim().length < 2) {
      setSuggestions([]);
      return;
    }

    debounceRef.current = setTimeout(async () => {
      const results = await suggest(value.trim(), suggestType);
      setSuggestions(results);
      setOpen(true);
    }, 300);

    return () => clearTimeout(debounceRef.current);
  }, [value, suggestType]);

  return (
    <div className="relative">
      <input
        type="text"
        value={value}
        onChange={(e) => onChange(e.target.value)}
        onFocus={() => suggestions.length > 0 && setOpen(true)}
        onBlur={() => setTimeout(() => setOpen(false), 150)}
        placeholder={placeholder}
        className="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 bg-white dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400"
        autoComplete="off"
      />
      {open && suggestions.length > 0 && (
        <ul className="absolute z-10 w-full mt-1 border border-gray-200 dark:border-gray-800 rounded-lg bg-white dark:bg-gray-900 shadow-lg max-h-64 overflow-y-auto">
          {suggestions.map((item) => (
            <li key={`${item.type}-${item.value}`}>
              <button
                type="button"
                onMouseDown={() => {
                  onSelectSuggestion(item);
                  setOpen(false);
                }}
                className="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-950"
              >
                {item.label}
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
