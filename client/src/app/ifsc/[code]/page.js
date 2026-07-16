import { notFound } from 'next/navigation';
import { getBranchByIfsc } from '@/lib/api';
import { bankOrCreditUnionJsonLd } from '@/lib/jsonld';
import AdSlot from '@/components/AdSlot';

export const revalidate = 86400;
export const dynamicParams = true;

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

function FlagRow({ label, active }) {
  return (
    <div className="flex justify-between border-b border-gray-100 dark:border-gray-800 py-2">
      <span className="text-gray-600 dark:text-gray-400">{label}</span>
      <span className={active ? 'text-green-700 font-medium' : 'text-gray-400'}>
        {active ? 'Available' : 'Not available'}
      </span>
    </div>
  );
}

export async function generateMetadata({ params }) {
  const { code } = await params;
  const branch = await getBranchByIfsc(code);

  if (!branch) {
    return { title: `IFSC ${code.toUpperCase()} not found` };
  }

  const title = `${branch.IFSC} — ${branch.BANK || branch.BANKCODE}, ${branch.BRANCH}`;
  const description = `IFSC code ${branch.IFSC} for ${branch.BANK || branch.BANKCODE} ${branch.BRANCH} branch in ${branch.CITY}, ${branch.STATE}. MICR: ${branch.MICR || 'N/A'}. NEFT: ${branch.NEFT ? 'Yes' : 'No'}, RTGS: ${branch.RTGS ? 'Yes' : 'No'}, IMPS: ${branch.IMPS ? 'Yes' : 'No'}.`;

  return {
    title,
    description,
    openGraph: { title, description, url: `${siteUrl}/ifsc/${branch.IFSC}` },
  };
}

export default async function IfscDetailPage({ params }) {
  const { code } = await params;
  const branch = await getBranchByIfsc(code);

  if (!branch) {
    notFound();
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
      <div>
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(bankOrCreditUnionJsonLd(branch, siteUrl)) }}
        />

        <p className="font-mono text-sm text-blue-700 dark:text-blue-300 mb-1">{branch.IFSC}</p>
        <h1 className="text-2xl font-bold mb-1">{branch.BANK || branch.BANKCODE}</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">{branch.BRANCH}</p>

        <div className="border border-gray-200 dark:border-gray-800 rounded-lg p-4 mb-6 space-y-1 text-sm">
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">Address</span>
            <span className="text-right max-w-[70%]">{branch.ADDRESS || 'N/A'}</span>
          </div>
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">City</span>
            <span>{branch.CITY || 'N/A'}</span>
          </div>
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">District</span>
            <span>{branch.DISTRICT || 'N/A'}</span>
          </div>
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">State</span>
            <span>{branch.STATE || 'N/A'}</span>
          </div>
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">MICR Code</span>
            <span>{branch.MICR || 'N/A'}</span>
          </div>
          <div className="flex justify-between py-1">
            <span className="text-gray-600 dark:text-gray-400">Contact</span>
            <span>{branch.CONTACT || 'N/A'}</span>
          </div>
        </div>

        <h2 className="font-semibold mb-2">Transaction Modes</h2>
        <div className="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
          <FlagRow label="NEFT" active={branch.NEFT} />
          <FlagRow label="RTGS" active={branch.RTGS} />
          <FlagRow label="IMPS" active={branch.IMPS} />
          <FlagRow label="UPI" active={branch.UPI} />
        </div>

        <AdSlot variant="inline" className="mt-8" />
      </div>
      <aside className="hidden lg:block">
        <AdSlot variant="sidebar" />
      </aside>
    </div>
  );
}
