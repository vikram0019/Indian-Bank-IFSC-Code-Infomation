import Link from 'next/link';
import { notFound } from 'next/navigation';
import { searchByBankAndCity } from '@/lib/api';
import ResultsTable from '@/components/ResultsTable';
import AdSlot from '@/components/AdSlot';

export const revalidate = 86400;
export const dynamicParams = true;

function unslugify(slug) {
  return decodeURIComponent(slug).replace(/-/g, ' ');
}

export async function generateMetadata({ params }) {
  const { bankcode, city } = await params;
  const bankNamePrefix = unslugify(bankcode);
  const cityName = unslugify(city);
  const data = await searchByBankAndCity(bankNamePrefix, cityName, { limit: 1 });
  const bankName = data.results?.[0]?.BANK || bankNamePrefix;

  const title = `${bankName} Branches in ${cityName} — IFSC Codes`;
  const description = `Browse all ${bankName} branches in ${cityName} with IFSC codes, addresses, and MICR codes.`;

  return { title, description };
}

export default async function BankCityPage({ params }) {
  const { bankcode, city } = await params;
  const bankNamePrefix = unslugify(bankcode);
  const cityName = unslugify(city);
  const data = await searchByBankAndCity(bankNamePrefix, cityName, { limit: 100 });

  if (!data.results || data.results.length === 0) {
    notFound();
  }

  const bankName = data.results[0].BANK || bankNamePrefix;

  return (
    <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
      <div>
        <Link href="/" className="text-sm text-blue-600 dark:text-blue-400 hover:underline">
          &larr; Back to search
        </Link>
        <h1 className="text-2xl font-bold mb-1 mt-2">
          {bankName} Branches in {cityName}
        </h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">{data.total} branch(es) found</p>

        <ResultsTable results={data.results} />
      </div>
      <aside className="hidden lg:block">
        <AdSlot variant="sidebar" />
      </aside>
    </div>
  );
}
