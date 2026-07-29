'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import SearchBar from './SearchBar';
import ResultsTable from './ResultsTable';
import { searchByBranchName } from '@/lib/api';

const TABS = [
  { id: 'ifsc', label: 'By IFSC Code' },
  { id: 'bank', label: 'By Bank + City' },
  { id: 'branch', label: 'By Branch Name' },
];

function slugify(value) {
  return value.trim().toLowerCase().replace(/\s+/g, '-');
}

export default function SearchTabs() {
  const [activeTab, setActiveTab] = useState('ifsc');
  const router = useRouter();

  const [ifscQuery, setIfscQuery] = useState('');

  const [bankQuery, setBankQuery] = useState('');
  const [bankName, setBankName] = useState(null);
  const [city, setCity] = useState('');

  const [branchQuery, setBranchQuery] = useState('');
  const [branchResults, setBranchResults] = useState(null);
  const [branchLoading, setBranchLoading] = useState(false);

  function handleIfscSubmit(e) {
    e.preventDefault();
    if (ifscQuery.trim()) router.push(`/ifsc/${ifscQuery.trim().toUpperCase()}`);
  }

  function handleBankSubmit(e) {
    e.preventDefault();
    if (bankName && city.trim()) {
      router.push(`/bank/${slugify(bankName)}/${slugify(city)}`);
    }
  }

  async function handleBranchSubmit(e) {
    e.preventDefault();
    if (!branchQuery.trim()) return;
    setBranchLoading(true);
    try {
      const data = await searchByBranchName(branchQuery.trim());
      setBranchResults(data.results || []);
    } finally {
      setBranchLoading(false);
    }
  }

  return (
    <div>
      <div className="flex gap-2 border-b border-gray-200 dark:border-gray-800 mb-4">
        {TABS.map((tab) => (
          <button
            key={tab.id}
            type="button"
            onClick={() => setActiveTab(tab.id)}
            className={`px-4 py-2 text-sm font-medium border-b-2 -mb-px ${
              activeTab === tab.id
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {activeTab === 'ifsc' && (
        <form onSubmit={handleIfscSubmit} className="flex flex-col sm:flex-row gap-2">
          <div className="flex-1">
            <SearchBar
              value={ifscQuery}
              onChange={setIfscQuery}
              suggestType="ifsc"
              onSelectSuggestion={(item) => router.push(`/ifsc/${item.value}`)}
              placeholder="Enter IFSC code e.g. SBIN0000001"
            />
          </div>
          <button type="submit" className="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
            Search
          </button>
        </form>
      )}

      {activeTab === 'bank' && (
        <form onSubmit={handleBankSubmit} className="flex flex-col sm:flex-row gap-2">
          <div className="flex-1">
            <SearchBar
              value={bankQuery}
              onChange={(v) => {
                setBankQuery(v);
                setBankName(null);
              }}
              suggestType="bank"
              onSelectSuggestion={(item) => {
                setBankQuery(item.label);
                setBankName(item.value);
              }}
              placeholder="Bank name e.g. State Bank of India"
            />
          </div>
          <div className="flex-1">
            <SearchBar
              value={city}
              onChange={setCity}
              suggestType="city"
              onSelectSuggestion={(item) => setCity(item.value)}
              placeholder="City or town e.g. Mumbai"
            />
          </div>
          <button
            type="submit"
            disabled={!bankName || !city.trim()}
            className="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg font-medium disabled:opacity-40"
          >
            Search
          </button>
        </form>
      )}
      {activeTab === 'bank' && !bankName && bankQuery && (
        <p className="text-xs text-gray-500 mt-2">Pick a bank from the suggestions to continue.</p>
      )}

      {activeTab === 'branch' && (
        <>
          <form onSubmit={handleBranchSubmit} className="flex flex-col sm:flex-row gap-2">
            <div className="flex-1">
              <SearchBar
                value={branchQuery}
                onChange={setBranchQuery}
                suggestType="branch"
                onSelectSuggestion={(item) => router.push(`/ifsc/${item.value}`)}
                placeholder="Branch name e.g. Fort Mumbai"
              />
            </div>
            <button type="submit" className="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">
              Search
            </button>
          </form>
          {branchLoading && <p className="text-sm text-gray-500 mt-3">Searching...</p>}
          {branchResults && !branchLoading && (
            <div className="mt-4">
              {branchResults.length === 0 ? (
                <p className="text-sm text-gray-500">No branches found for &quot;{branchQuery}&quot;.</p>
              ) : (
                <ResultsTable results={branchResults} />
              )}
            </div>
          )}
        </>
      )}
    </div>
  );
}
