'use client';

import { useState } from 'react';
import { faqPageJsonLd } from '@/lib/jsonld';

export default function FaqAccordion({ faqs }) {
  const [openIndex, setOpenIndex] = useState(null);

  return (
    <div>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqPageJsonLd(faqs)) }}
      />
      <div className="space-y-3">
        {faqs.map((faq, i) => (
          <div key={faq.question} className="border border-gray-200 dark:border-gray-800 rounded-lg">
            <button
              type="button"
              onClick={() => setOpenIndex(openIndex === i ? null : i)}
              className="w-full text-left px-4 py-3 font-medium flex justify-between items-center"
              aria-expanded={openIndex === i}
            >
              {faq.question}
              <span>{openIndex === i ? '−' : '+'}</span>
            </button>
            {openIndex === i && (
              <p className="px-4 pb-3 text-sm text-gray-600 dark:text-gray-400">{faq.answer}</p>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
