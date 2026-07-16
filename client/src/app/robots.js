import { getSitemapPage } from '@/lib/api';

const CHUNK_SIZE = 5000;
const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

export default async function robots() {
  const first = await getSitemapPage(0, 1);
  const numChunks = Math.max(Math.ceil(first.total / CHUNK_SIZE), 1);
  const sitemaps = Array.from({ length: numChunks }, (_, i) => `${siteUrl}/sitemap/${i}.xml`);

  return {
    rules: { userAgent: '*', allow: '/', disallow: '/admin' },
    sitemap: sitemaps,
  };
}
