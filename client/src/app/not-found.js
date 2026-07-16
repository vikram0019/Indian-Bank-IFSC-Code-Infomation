import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="text-center py-24">
      <h1 className="text-2xl font-bold mb-2">Not found</h1>
      <p className="text-gray-600 dark:text-gray-400 mb-6">
        We couldn&apos;t find what you were looking for.
      </p>
      <Link href="/" className="text-blue-600 font-medium">
        Back to search
      </Link>
    </div>
  );
}
