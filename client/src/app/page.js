import Link from 'next/link';
import SearchTabs from '@/components/SearchTabs';
import FaqAccordion from '@/components/FaqAccordion';
import PopularBanks from '@/components/PopularBanks';
import AdSlot from '@/components/AdSlot';
import { faqs } from '@/lib/faqData';

export default function Home() {
  return (
    <div>
      <section className="text-center mb-8">
        <h1 className="text-3xl font-bold mb-2">Find Any Indian Bank Branch&apos;s IFSC Code</h1>
        <p className="text-gray-600 dark:text-gray-400">
          Search by IFSC code, bank + city, or branch name to get MICR, address, and NEFT/RTGS/IMPS/UPI details.
        </p>
      </section>

      <div className="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
        <div>
          <SearchTabs />
        </div>
        <aside className="hidden lg:block">
          <AdSlot variant="sidebar" />
        </aside>
      </div>

      <section className="mt-16">
        <PopularBanks />
      </section>

      <section className="mt-16">
        <h2 className="text-xl font-semibold mb-4">Frequently Asked Questions</h2>
        <FaqAccordion faqs={faqs} />
        <p className="text-sm mt-4">
          <Link href="/faq" className="text-blue-600 dark:text-blue-400 hover:underline">
            Read the full IFSC &amp; MICR guide &rarr;
          </Link>
        </p>
      </section>
    </div>
  );
}
