import Link from 'next/link';
import FaqAccordion from '@/components/FaqAccordion';
import PopularBanks from '@/components/PopularBanks';
import TableOfContents from '@/components/TableOfContents';
import { faqs } from '@/lib/faqData';

export const metadata = {
  title: 'IFSC & MICR Code Guide — What They Are & How to Find Them',
  description:
    'A complete guide to IFSC and MICR codes: what they are, how to find them, NEFT/RTGS/IMPS charges and timings, and frequently asked questions.',
};

const TOC_ITEMS = [
  { id: 'what-is-ifsc', label: 'What is IFSC code?' },
  { id: 'how-to-find', label: 'How to find IFSC code?' },
  { id: 'benefits-ifsc', label: 'Benefits of IFSC code' },
  { id: 'benefits-micr', label: 'Benefits of MICR code' },
  { id: 'charges', label: 'NEFT/RTGS/IMPS charges & timing' },
  { id: 'how-to-transfer', label: 'How to transfer money with IFSC' },
  { id: 'ifsc-vs-micr', label: 'Difference: IFSC vs MICR' },
  { id: 'faq', label: 'FAQs' },
  { id: 'popular-banks', label: 'Popular banks' },
];

function Section({ id, title, children }) {
  return (
    <section id={id} className="mb-12 scroll-mt-20">
      {title && <h2 className="text-xl font-semibold mb-3">{title}</h2>}
      {children}
    </section>
  );
}

export default function FaqPage() {
  return (
    <div>
      <Link href="/" className="text-sm text-blue-600 dark:text-blue-400 hover:underline">
        &larr; Back to Home
      </Link>
      <h1 className="text-2xl font-bold mb-8 mt-3">IFSC &amp; MICR Code Guide</h1>

      <div className="grid grid-cols-1 lg:grid-cols-[1fr_220px] gap-10">
        <div className="max-w-2xl">
          <Section id="what-is-ifsc" title="What is IFSC code?">
            <p className="text-gray-700 dark:text-gray-300">
              IFSC (Indian Financial System Code) is a unique 11-character alphanumeric code
              allotted by the Reserve Bank of India (RBI) to every bank branch in India. It is
              required for electronic fund transfers — NEFT, RTGS, and IMPS. The first 4
              characters identify the bank, the 5th character is always &quot;0&quot;, and the
              last 6 characters identify the specific branch.
            </p>
          </Section>

          <Section id="how-to-find" title="How to find IFSC code?">
            <ul className="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
              <li>Check your bank passbook or cheque leaf — the IFSC is usually printed there.</li>
              <li>Search by IFSC code, bank + city, or branch name on this site.</li>
              <li>Check the bank&apos;s official website for a branch locator.</li>
            </ul>
          </Section>

          <Section id="benefits-ifsc" title="Benefits of IFSC code">
            <ul className="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
              <li>Uniquely identifies a bank and its exact branch.</li>
              <li>Reduces errors during electronic fund transfers.</li>
              <li>Required and validated for NEFT, RTGS, and IMPS transactions.</li>
            </ul>
          </Section>

          <Section id="benefits-micr" title="Benefits of MICR code">
            <p className="text-gray-700 dark:text-gray-300">
              MICR (Magnetic Ink Character Recognition) code enables fast, accurate, machine
              processing of cheques using magnetic ink and optical character recognition,
              reducing manual handling errors in cheque clearing.
            </p>
          </Section>

          <Section id="charges" title="NEFT / RTGS / IMPS charges & timing">
            <p className="text-sm text-gray-500 mb-3">
              Charges are indicative — each bank sets its own fees within RBI limits. Always
              confirm with your bank.
            </p>
            <div className="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
              <table className="w-full text-sm">
                <thead>
                  <tr className="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                    <th className="px-4 py-2 font-medium">Amount</th>
                    <th className="px-4 py-2 font-medium">NEFT</th>
                    <th className="px-4 py-2 font-medium">RTGS</th>
                    <th className="px-4 py-2 font-medium">IMPS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">Up to &#8377;10,000</td>
                    <td className="px-4 py-2">&#8377;2.50</td>
                    <td className="px-4 py-2">Min. &#8377;2 Lakh</td>
                    <td className="px-4 py-2">&#8377;5.00</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">&#8377;10,000 &ndash; &#8377;2 Lakh</td>
                    <td className="px-4 py-2">&#8377;15.00</td>
                    <td className="px-4 py-2">&#8377;26.00</td>
                    <td className="px-4 py-2">&#8377;10.00</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">Above &#8377;2 Lakh</td>
                    <td className="px-4 py-2">&#8377;25.00</td>
                    <td className="px-4 py-2">&#8377;51.00</td>
                    <td className="px-4 py-2">&#8377;15.00</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <ul className="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300 mt-3 text-sm">
              <li>NEFT: 8 AM &ndash; 7 PM (weekdays)</li>
              <li>RTGS: 9 AM &ndash; 4:30 PM (weekdays)</li>
              <li>IMPS: 24 hours, 365 days</li>
            </ul>
          </Section>

          <Section id="how-to-transfer" title="How to transfer money with IFSC code">
            <p className="text-gray-700 dark:text-gray-300 mb-3">
              The IFSC code tells the banking system exactly which bank and branch to route a
              transfer to:
            </p>
            <ol className="list-decimal pl-5 space-y-1 text-gray-700 dark:text-gray-300">
              <li>The first 4 characters identify the bank (e.g. ICIC for ICICI Bank).</li>
              <li>The 5th character is always &quot;0&quot;.</li>
              <li>The last 6 characters identify the specific branch.</li>
            </ol>
            <p className="text-gray-700 dark:text-gray-300 mt-3">
              To send money, the payer provides the payee&apos;s bank name, branch, account
              number, and IFSC code via NEFT, RTGS, or IMPS. The transfer is then routed directly
              to the correct branch.
            </p>
          </Section>

          <Section id="ifsc-vs-micr" title="Difference between IFSC and MICR code">
            <div className="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
              <table className="w-full text-sm">
                <thead>
                  <tr className="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                    <th className="px-4 py-2 font-medium">IFSC Code</th>
                    <th className="px-4 py-2 font-medium">MICR Code</th>
                  </tr>
                </thead>
                <tbody>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">Used for electronic transfers (NEFT/RTGS/IMPS)</td>
                    <td className="px-4 py-2">Used for cheque processing</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">11-character alphanumeric code</td>
                    <td className="px-4 py-2">9-digit numeric code</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">First 4 characters identify the bank</td>
                    <td className="px-4 py-2">First 3 digits identify the city</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2">Last 6 characters identify the branch</td>
                    <td className="px-4 py-2">Last 3 digits identify the branch</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </Section>

          <Section id="faq" title="Frequently Asked Questions">
            <FaqAccordion faqs={faqs} />
          </Section>

          <Section id="popular-banks" title="">
            <PopularBanks />
          </Section>
        </div>

        <TableOfContents items={TOC_ITEMS} />
      </div>
    </div>
  );
}
