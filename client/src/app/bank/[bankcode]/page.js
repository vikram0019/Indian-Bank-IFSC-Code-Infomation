import Link from 'next/link';
import { notFound } from 'next/navigation';
import { getCitiesForBank } from '@/lib/api';
import BankCityFilter from '@/components/BankCityFilter';
import AdSlot from '@/components/AdSlot';

export const revalidate = 86400;
export const dynamicParams = true;

function unslugify(slug) {
  return decodeURIComponent(slug).replace(/-/g, ' ');
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
        <p className="text-gray-600 dark:text-gray-400 mb-4">
          Select a city or town to see {data.bank} branches and their IFSC codes.
        </p>

        <BankCityFilter bankSlug={bankcode} cities={data.cities} />
      </div>
      <aside className="hidden lg:block">
        <AdSlot variant="sidebar" />
      </aside>
    </div>
  );
}
