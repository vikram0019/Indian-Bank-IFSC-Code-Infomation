<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FAQ + guide content, ported verbatim from client/src/lib/faqData.js and
 * client/src/app/faq/page.js. This copy was deliberately rewritten in original wording
 * and structure (not following any external reference site) — port as-is, do not
 * regenerate from any external source.
 */

function ifsc_finder_get_faqs()
{
    return [
        [
            'question' => 'What is an IFSC code?',
            'answer' => 'IFSC (Indian Financial System Code) is an 11-character alphanumeric code that uniquely identifies a bank branch participating in NEFT, RTGS, and IMPS electronic funds transfer systems in India. The first 4 characters identify the bank, the 5th is always "0", and the last 6 identify the branch.',
        ],
        [
            'question' => 'How do I find my MICR code?',
            'answer' => 'The MICR (Magnetic Ink Character Recognition) code is a 9-digit code printed on your cheque leaf. You can also look it up by searching for your branch\'s IFSC code on this site — the branch detail page shows the MICR code alongside other branch details.',
        ],
        [
            'question' => 'Is the IFSC code the same for all branches of a bank?',
            'answer' => 'No. Each branch of a bank has its own unique IFSC code. Even branches of the same bank in the same city will have different IFSC codes.',
        ],
        [
            'question' => 'Can I use the same IFSC code for NEFT, RTGS, and IMPS?',
            'answer' => 'Yes, the IFSC code is the same across NEFT, RTGS, and IMPS — but not every branch supports every mode. Check the branch detail page on this site for which transfer modes are available at a specific branch.',
        ],
        [
            'question' => 'Is the IFSC code the same as the branch code?',
            'answer' => 'No. An IFSC code and a branch code are different things, though related. The branch code is usually the last 6 characters of the IFSC — a shorter identifier used internally by some banks — while the IFSC is the full 11-character code required for NEFT/RTGS/IMPS transfers.',
        ],
        [
            'question' => 'What is a branch code?',
            'answer' => "A branch code narrows down a single location within a bank, the way a house number narrows down an address on a street. It's a shorter, informal identifier some banks use internally, separate from the full IFSC that online transfer systems actually require.",
        ],
        [
            'question' => 'Where can I find my IFSC code?',
            'answer' => "Your IFSC code is printed on your bank passbook and cheque leaf. You can also search for it on this site by bank name and city, or by branch name.",
        ],
        [
            'question' => 'Does the IFSC code change if a bank merges with another bank?',
            'answer' => 'Yes. When banks merge, branches are usually migrated to the acquiring bank\'s IFSC codes over time, so an old IFSC code may stop working. Always verify the current IFSC code before making a transfer if the bank has recently been involved in a merger.',
        ],
    ];
}

function ifsc_finder_get_toc()
{
    return [
        ['id' => 'ifsc-explained', 'label' => 'IFSC codes, explained'],
        ['id' => 'finding-your-code', 'label' => 'Finding your code'],
        ['id' => 'why-it-matters', 'label' => 'Why it matters'],
        ['id' => 'micr-code', 'label' => 'MICR: the cheque code'],
        ['id' => 'transfer-methods', 'label' => 'NEFT vs RTGS vs IMPS'],
        ['id' => 'how-it-works', 'label' => 'How a transfer is routed'],
        ['id' => 'faq', 'label' => 'FAQs'],
        ['id' => 'popular-banks', 'label' => 'Popular banks'],
    ];
}

function ifsc_finder_get_popular_banks()
{
    $names = [
        'State Bank of India', 'HDFC Bank', 'ICICI Bank', 'Axis Bank',
        'Punjab National Bank', 'Bank of Baroda', 'Canara Bank', 'Union Bank of India',
        'Kotak Mahindra Bank', 'IndusInd Bank', 'Yes Bank', 'IDBI Bank',
        'Bank of India', 'Central Bank of India', 'UCO Bank', 'Indian Bank',
        'Indian Overseas Bank', 'Federal Bank', 'South Indian Bank', 'RBL Bank',
    ];

    return array_map(function ($name) {
        return ['name' => $name, 'slug' => Ifsc_Rewrite::slugify($name)];
    }, $names);
}
