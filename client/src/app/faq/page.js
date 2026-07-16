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
  { id: 'ifsc-explained', label: 'IFSC codes, explained' },
  { id: 'finding-your-code', label: 'Finding your code' },
  { id: 'why-it-matters', label: 'Why it matters' },
  { id: 'micr-code', label: 'MICR: the cheque code' },
  { id: 'transfer-methods', label: 'NEFT vs RTGS vs IMPS' },
  { id: 'how-it-works', label: 'How a transfer is routed' },
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
          <Section id="ifsc-explained" title="IFSC codes, explained">
            <p className="text-gray-700 dark:text-gray-300">
              Every bank branch in India that participates in electronic payments carries an
              eleven-character code called an IFSC (Indian Financial System Code), issued by the
              Reserve Bank of India. Read left to right, it packs in three pieces of information:
              the first four letters name the bank, a fixed &quot;0&quot; sits in the fifth spot
              as a separator, and the remaining six characters pin down the exact branch. Banking
              systems rely on this code, rather than a branch address, to route NEFT, RTGS, and
              IMPS transfers to the right place.
            </p>
          </Section>

          <Section id="finding-your-code" title="Finding your code">
            <p className="text-gray-700 dark:text-gray-300 mb-3">
              The fastest option is usually the one you&apos;re already using: type a bank name
              and city, a branch name, or the code itself into the search box on this page and
              you&apos;ll get the branch record directly.
            </p>
            <p className="text-gray-700 dark:text-gray-300">
              If you&apos;d rather confirm it another way, it&apos;s also printed on the first
              page of your passbook and along the bottom of every cheque leaf, and most banks
              list it in the branch locator on their own website.
            </p>
          </Section>

          <Section id="why-it-matters" title="Why it matters">
            <p className="text-gray-700 dark:text-gray-300">
              Two branches of the same bank — even two branches in the same city — never share an
              IFSC, so the code is the one piece of routing information a transfer can&apos;t
              succeed without. Get the account number right but the IFSC wrong and the payment
              either bounces back or, worse, lands at an unintended branch. Entering it correctly
              is what lets NEFT, RTGS, and IMPS confirm a transfer in minutes rather than needing
              manual verification.
            </p>
          </Section>

          <Section id="micr-code" title="MICR: the cheque code">
            <p className="text-gray-700 dark:text-gray-300 mb-4">
              MICR (Magnetic Ink Character Recognition) solves a similar problem for paper
              cheques instead of electronic transfers. It&apos;s a nine-digit number printed in
              special magnetic ink so that cheque-sorting machines can read it optically without
              manual data entry, which is what lets banks clear large volumes of cheques quickly
              and with fewer errors.
            </p>
            <div className="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
              <table className="w-full text-sm">
                <thead>
                  <tr className="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                    <th className="px-4 py-2 font-medium"> </th>
                    <th className="px-4 py-2 font-medium">IFSC</th>
                    <th className="px-4 py-2 font-medium">MICR</th>
                  </tr>
                </thead>
                <tbody>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 text-gray-500">Used for</td>
                    <td className="px-4 py-2">Online transfers &mdash; NEFT, RTGS, IMPS</td>
                    <td className="px-4 py-2">Cheque clearing</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 text-gray-500">Format</td>
                    <td className="px-4 py-2">11 characters, letters + digits</td>
                    <td className="px-4 py-2">9 digits</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 text-gray-500">First part identifies</td>
                    <td className="px-4 py-2">The bank</td>
                    <td className="px-4 py-2">The city</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 text-gray-500">Last part identifies</td>
                    <td className="px-4 py-2">The branch</td>
                    <td className="px-4 py-2">The branch</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </Section>

          <Section id="transfer-methods" title="NEFT vs RTGS vs IMPS">
            <p className="text-gray-700 dark:text-gray-300 mb-3">
              All three move money using the same IFSC, but they differ in when they run and what
              a bank is allowed to charge for them. Figures below are typical fee ceilings — your
              bank sets its own charges under these limits, and many banks now waive NEFT fees
              entirely for savings accounts, so treat this as a rough guide rather than a quote.
            </p>
            <div className="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
              <table className="w-full text-sm">
                <thead>
                  <tr className="bg-gray-50 dark:bg-gray-900 text-left text-gray-600 dark:text-gray-400">
                    <th className="px-4 py-2 font-medium">Method</th>
                    <th className="px-4 py-2 font-medium">Available</th>
                    <th className="px-4 py-2 font-medium">Typical fee (up to &#8377;10k)</th>
                    <th className="px-4 py-2 font-medium">Typical fee (above &#8377;2L)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 font-medium">NEFT</td>
                    <td className="px-4 py-2">8 AM&ndash;7 PM, weekdays</td>
                    <td className="px-4 py-2">&#8377;2&ndash;3</td>
                    <td className="px-4 py-2">up to &#8377;25</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 font-medium">RTGS</td>
                    <td className="px-4 py-2">9 AM&ndash;4:30 PM, weekdays</td>
                    <td className="px-4 py-2">not applicable (&#8377;2L minimum)</td>
                    <td className="px-4 py-2">up to &#8377;50</td>
                  </tr>
                  <tr className="border-t border-gray-100 dark:border-gray-800">
                    <td className="px-4 py-2 font-medium">IMPS</td>
                    <td className="px-4 py-2">24&times;7, every day</td>
                    <td className="px-4 py-2">&#8377;5&ndash;10</td>
                    <td className="px-4 py-2">up to &#8377;15</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </Section>

          <Section id="how-it-works" title="How a transfer is routed">
            <p className="text-gray-700 dark:text-gray-300">
              When you send money, your bank&apos;s system reads the recipient&apos;s IFSC in two
              stages: the bank-identifying prefix tells it which clearing network to hand the
              payment off to, and once it arrives at that bank, the branch-identifying suffix
              tells that bank&apos;s internal systems which branch — and therefore which account
              — should receive it. That&apos;s the whole reason the code needs to be exact:
              a single wrong character can point a payment at a completely different branch.
            </p>
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
