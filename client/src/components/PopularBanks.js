import Link from 'next/link';
import { popularBanks } from '@/lib/popularBanks';

export default function PopularBanks() {
  return (
    <section>
      <h2 className="text-xl font-semibold mb-4">Popular Banks</h2>
      <div className="flex flex-wrap gap-2">
        {popularBanks.map((bank) => (
          <Link
            key={bank.slug}
            href={`/bank/${bank.slug}`}
            className="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-800 rounded-full hover:border-blue-400 hover:text-blue-600 dark:hover:text-blue-400"
          >
            {bank.name}
          </Link>
        ))}
      </div>
    </section>
  );
}
