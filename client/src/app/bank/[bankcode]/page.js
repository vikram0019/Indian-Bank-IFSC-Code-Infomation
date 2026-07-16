import Link from 'next/link';
import { notFound } from 'next/navigation';
import { getCitiesForBank } from '@/lib/api';
import AdSlot from '@/components/AdSlot';

export const revalidate = 86400;
export const dynamicParams = true;

function unslugify(slug) {
  return decodeURIComponent(slug).replace(/-/g, ' ');
}

function slugify(value) {
  return value.trim().toLowerCase().replace(/\s+/g, '-');
}

export async function generateMetadata({ params }) {
  const { bankcode } = await params;
  const bankNamePrefix = unslugify(bankcode);
  const data = await getCitiesForBank(bankNamePrefix);

  const title = `${data.bank} IFSC Codes — Find Branches by City`;
  const description = `Browse ${data.bank} branches by city or town and find IFSC codes, MICR codes, and NEFT/RTGS/IMPS availability.`;

  return { title, description };
}

export default async function BankOverviewPage({ params }) {
  const { bankcode } = await params;
  const bankNamePrefix = unslugify(bankcode);
  const data = await getCitiesForBank(bankNamePrefix);

  if (!data.cities || data.cities.length === 0) {
    notFound();
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
      <div>
        <Link href="/" className="text-sm text-blue-600 dark:text-blue-400 hover:underline">
          &larr; Back to search
        </Link>
        <h1 className="text-2xl font-bold mb-1 mt-2">{data.bank}</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">
          Select a city or town to see {data.bank} branches and their IFSC codes.
        </p>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
          {data.cities.map((c) => (
            <Link
              key={`${c.city}-${c.state}`}
              href={`/bank/${bankcode}/${slugify(c.city)}`}
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
      </div>
      <aside className="hidden lg:block">
        <AdSlot variant="sidebar" />
      </aside>
    </div>
  );
}
