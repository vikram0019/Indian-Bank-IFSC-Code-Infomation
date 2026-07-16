import Link from 'next/link';

function FlagBadge({ label, active }) {
  return (
    <span
      className={`px-2 py-0.5 rounded text-xs font-medium ${
        active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400'
      }`}
    >
      {label}
    </span>
  );
}

export default function BranchCard({ branch }) {
  return (
    <Link
      href={`/ifsc/${branch.IFSC}`}
      className="block border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-blue-400 transition-colors"
    >
      <div className="flex items-center justify-between gap-2 flex-wrap">
        <div>
          <p className="font-semibold">{branch.BANK || branch.BANKCODE}</p>
          <p className="text-sm text-gray-600 dark:text-gray-400">{branch.BRANCH}</p>
        </div>
        <span className="font-mono text-sm bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">
          {branch.IFSC}
        </span>
      </div>
      <p className="text-sm text-gray-500 mt-2">
        {branch.CITY}, {branch.STATE}
      </p>
      <div className="flex gap-2 mt-3">
        <FlagBadge label="NEFT" active={branch.NEFT} />
        <FlagBadge label="RTGS" active={branch.RTGS} />
        <FlagBadge label="IMPS" active={branch.IMPS} />
        <FlagBadge label="UPI" active={branch.UPI} />
      </div>
    </Link>
  );
}
