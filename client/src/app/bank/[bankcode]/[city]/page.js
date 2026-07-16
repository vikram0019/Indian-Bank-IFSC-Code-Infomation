import { notFound } from 'next/navigation';
import { searchByBankAndCity } from '@/lib/api';
import BranchCard from '@/components/BranchCard';
import AdSlot from '@/components/AdSlot';

export const revalidate = 86400;
export const dynamicParams = true;

function unslugifyCity(citySlug) {
  return decodeURIComponent(citySlug).replace(/-/g, ' ');
}

export async function generateMetadata({ params }) {
  const { bankcode, city } = await params;
  const cityName = unslugifyCity(city);
  const data = await searchByBankAndCity(bankcode, cityName, { limit: 1 });
  const bankName = data.results?.[0]?.BANK || bankcode.toUpperCase();

  const title = `${bankName} Branches in ${cityName} — IFSC Codes`;
  const description = `Browse all ${bankName} branches in ${cityName} with IFSC codes, addresses, and MICR codes.`;

  return { title, description };
}

export default async function BankCityPage({ params }) {
  const { bankcode, city } = await params;
  const cityName = unslugifyCity(city);
  const data = await searchByBankAndCity(bankcode, cityName, { limit: 100 });

  if (!data.results || data.results.length === 0) {
    notFound();
  }

  const bankName = data.results[0].BANK || bankcode.toUpperCase();

  return (
    <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
      <div>
        <h1 className="text-2xl font-bold mb-1">
          {bankName} Branches in {cityName}
        </h1>
        <p className="text-gray-600 dark:text-gray-400 mb-6">{data.total} branch(es) found</p>

        <div className="space-y-3">
          {data.results.map((branch) => (
            <BranchCard key={branch.IFSC} branch={branch} />
          ))}
        </div>
      </div>
      <aside className="hidden lg:block">
        <AdSlot variant="sidebar" />
      </aside>
    </div>
  );
}
