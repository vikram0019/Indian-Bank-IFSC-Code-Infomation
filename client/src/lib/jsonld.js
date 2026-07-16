export function bankOrCreditUnionJsonLd(branch, siteUrl) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BankOrCreditUnion',
    name: branch.BANK || branch.BANKCODE,
    branchCode: branch.IFSC,
    url: `${siteUrl}/ifsc/${branch.IFSC}`,
    address: {
      '@type': 'PostalAddress',
      streetAddress: branch.ADDRESS,
      addressLocality: branch.CITY,
      addressRegion: branch.STATE,
      addressCountry: 'IN',
    },
    ...(branch.CONTACT ? { telephone: branch.CONTACT } : {}),
  };
}

export function faqPageJsonLd(faqs) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((faq) => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  };
}
