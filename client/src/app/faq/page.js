import FaqAccordion from '@/components/FaqAccordion';
import { faqs } from '@/lib/faqData';

export const metadata = {
  title: 'FAQ — IFSC, MICR & Bank Transfers',
  description: 'Frequently asked questions about IFSC codes, MICR codes, and NEFT/RTGS/IMPS transfers.',
};

export default function FaqPage() {
  return (
    <div className="max-w-2xl mx-auto">
      <h1 className="text-2xl font-bold mb-6">Frequently Asked Questions</h1>
      <FaqAccordion faqs={faqs} />
    </div>
  );
}
