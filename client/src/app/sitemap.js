import { getSitemapPage } from '@/lib/api';

const CHUNK_SIZE = 5000;
const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000';

export async function generateSitemaps() {
  const first = await getSitemapPage(0, 1);
  const numChunks = Math.max(Math.ceil(first.total / CHUNK_SIZE), 1);
  return Array.from({ length: numChunks }, (_, i) => ({ id: i }));
}

export default async function sitemap({ id }) {
  const chunkId = parseInt(await id, 10);
  const { items } = await getSitemapPage(chunkId * CHUNK_SIZE, CHUNK_SIZE);

  return items.map((item) => ({
    url: `${siteUrl}/ifsc/${item.ifsc}`,
    changeFrequency: 'monthly',
  }));
}
