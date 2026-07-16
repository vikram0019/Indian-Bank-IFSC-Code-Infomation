import Link from 'next/link';
import FlagBadge from './FlagBadge';

export default function ResultsTable({ results }) {
  if (!results || results.length === 0) return null;

  return (
    <div className="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
            <th scope="col" className="px-4 py-2 font-medium">IFSC Code</th>
            <th scope="col" className="px-4 py-2 font-medium">Bank</th>
            <th scope="col" className="px-4 py-2 font-medium">Branch</th>
            <th scope="col" className="px-4 py-2 font-medium">City, State</th>
            <th scope="col" className="px-4 py-2 font-medium">Transaction Modes</th>
          </tr>
        </thead>
        <tbody>
          {results.map((branch) => (
            <tr
              key={branch.IFSC}
              className="border-t border-gray-100 dark:border-gray-800 hover:bg-blue-50/50 dark:hover:bg-blue-950/30"
            >
              <td className="px-4 py-3">
                <Link
                  href={`/ifsc/${branch.IFSC}`}
                  className="font-mono text-blue-700 dark:text-blue-300 hover:underline"
                >
                  {branch.IFSC}
                </Link>
              </td>
              <td className="px-4 py-3">{branch.BANK || branch.BANKCODE}</td>
              <td className="px-4 py-3">{branch.BRANCH}</td>
              <td className="px-4 py-3 text-gray-600 dark:text-gray-400">
                {branch.CITY}, {branch.STATE}
              </td>
              <td className="px-4 py-3">
                <div className="flex flex-wrap gap-1.5">
                  <FlagBadge label="NEFT" active={branch.NEFT} />
                  <FlagBadge label="RTGS" active={branch.RTGS} />
                  <FlagBadge label="IMPS" active={branch.IMPS} />
                  <FlagBadge label="UPI" active={branch.UPI} />
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
