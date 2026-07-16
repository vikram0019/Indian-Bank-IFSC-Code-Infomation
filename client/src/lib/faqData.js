export const faqs = [
  {
    question: 'What is an IFSC code?',
    answer:
      'IFSC (Indian Financial System Code) is an 11-character alphanumeric code that uniquely identifies a bank branch participating in NEFT, RTGS, and IMPS electronic funds transfer systems in India. The first 4 characters identify the bank, the 5th is always "0", and the last 6 identify the branch.',
  },
  {
    question: 'How do I find my MICR code?',
    answer:
      'The MICR (Magnetic Ink Character Recognition) code is a 9-digit code printed on your cheque leaf. You can also look it up by searching for your branch\'s IFSC code on this site — the branch detail page shows the MICR code alongside other branch details.',
  },
  {
    question: 'Is the IFSC code the same for all branches of a bank?',
    answer:
      'No. Each branch of a bank has its own unique IFSC code. Even branches of the same bank in the same city will have different IFSC codes.',
  },
  {
    question: 'Can I use the same IFSC code for NEFT, RTGS, and IMPS?',
    answer:
      'Yes, the IFSC code is the same across NEFT, RTGS, and IMPS — but not every branch supports every mode. Check the branch detail page on this site for which transfer modes are available at a specific branch.',
  },
];
