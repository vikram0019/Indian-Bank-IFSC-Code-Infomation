function slugify(value) {
  return value.trim().toLowerCase().replace(/\s+/g, '-');
}

const BANK_NAMES = [
  'State Bank of India',
  'HDFC Bank',
  'ICICI Bank',
  'Axis Bank',
  'Punjab National Bank',
  'Bank of Baroda',
  'Canara Bank',
  'Union Bank of India',
  'Kotak Mahindra Bank',
  'IndusInd Bank',
  'Yes Bank',
  'IDBI Bank',
  'Bank of India',
  'Central Bank of India',
  'UCO Bank',
  'Indian Bank',
  'Indian Overseas Bank',
  'Federal Bank',
  'South Indian Bank',
  'RBL Bank',
];

export const popularBanks = BANK_NAMES.map((name) => ({ name, slug: slugify(name) }));
