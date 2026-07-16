import { Geist, Geist_Mono } from 'next/font/google';
import Link from 'next/link';
import './globals.css';
import AdSlot from '@/components/AdSlot';

const geistSans = Geist({
  variable: '--font-geist-sans',
  subsets: ['latin'],
});

const geistMono = Geist_Mono({
  variable: '--font-geist-mono',
  subsets: ['latin'],
});

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

export const metadata = {
  metadataBase: new URL(siteUrl),
  title: {
    default: 'Indian Bank IFSC Code Finder',
    template: '%s | IFSC Finder',
  },
  description:
    'Search Indian bank branch IFSC codes, MICR codes, addresses, and NEFT/RTGS/IMPS/UPI availability.',
};

export default function RootLayout({ children }) {
  return (
    <html
      lang="en"
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col">
        <header className="border-b border-gray-200 dark:border-gray-800">
          <div className="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <Link href="/" className="font-bold text-lg">
              IFSC Finder
            </Link>
            <nav className="text-sm flex items-center gap-4">
              <Link
                href="/"
                className="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700"
              >
                🔍 New Search
              </Link>
              <Link href="/faq" className="text-gray-600 dark:text-gray-400 hover:underline">
                FAQ
              </Link>
            </nav>
          </div>
        </header>

        <main className="flex-1 max-w-5xl mx-auto px-4 py-8 w-full">{children}</main>

        <div className="max-w-5xl mx-auto px-4">
          <AdSlot variant="footer" className="mb-4" />
        </div>

        <footer className="border-t border-gray-200 dark:border-gray-800 py-6">
          <div className="max-w-5xl mx-auto px-4 text-xs text-gray-500 flex justify-between flex-wrap gap-2">
            <span>&copy; {new Date().getFullYear()} IFSC Finder. Informational only — not affiliated with RBI.</span>
            <Link href="/admin">Admin</Link>
          </div>
        </footer>
      </body>
    </html>
  );
}
