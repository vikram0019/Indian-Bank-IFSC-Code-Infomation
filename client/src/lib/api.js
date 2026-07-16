const SERVER_API_URL = process.env.API_URL || 'http://localhost:4000';
export const PUBLIC_API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:4000';

// Server Components / generateMetadata run on the server, so they use API_URL.
// Client Components (e.g. the live-typing search bar) must use NEXT_PUBLIC_API_URL instead.
function baseUrl() {
  return typeof window === 'undefined' ? SERVER_API_URL : PUBLIC_API_URL;
}

async function apiFetch(path, options = {}) {
  const res = await fetch(`${baseUrl()}${path}`, {
    next: { revalidate: 86400 },
    ...options,
  });
  return res;
}

export async function getBranchByIfsc(code) {
  const res = await apiFetch(`/api/ifsc/${encodeURIComponent(code)}`);
  if (res.status === 404) return null;
  if (!res.ok) throw new Error(`Failed to fetch IFSC ${code}: ${res.status}`);
  return res.json();
}

export async function searchByBankAndCity(bank, city, params = {}) {
  const qs = new URLSearchParams({ bank, city, ...params });
  const res = await apiFetch(`/api/search?${qs.toString()}`);
  if (!res.ok) throw new Error(`Failed to search: ${res.status}`);
  return res.json();
}

export async function searchByBranchName(name, params = {}) {
  const qs = new URLSearchParams({ name, ...params });
  const res = await apiFetch(`/api/branch/search?${qs.toString()}`);
  if (!res.ok) throw new Error(`Failed to search: ${res.status}`);
  return res.json();
}

export async function suggest(q, type) {
  const qs = new URLSearchParams({ q, ...(type ? { type } : {}) });
  const res = await fetch(`${PUBLIC_API_URL}/api/suggest?${qs.toString()}`, { cache: 'no-store' });
  if (!res.ok) return [];
  return res.json();
}

export async function getSitemapPage(cursor, limit) {
  const qs = new URLSearchParams({ cursor: String(cursor || 0), ...(limit ? { limit } : {}) });
  const res = await apiFetch(`/api/sitemap-data?${qs.toString()}`, { next: { revalidate: 3600 } });
  if (!res.ok) throw new Error(`Failed to fetch sitemap data: ${res.status}`);
  return res.json();
}

export async function adminStats(secret) {
  const res = await fetch(`${PUBLIC_API_URL}/api/admin/stats`, {
    headers: { 'x-admin-secret': secret },
    cache: 'no-store',
  });
  if (!res.ok) throw new Error(res.status === 401 ? 'Invalid admin secret' : 'Failed to load stats');
  return res.json();
}

export async function adminRefresh(secret, limit) {
  const res = await fetch(`${PUBLIC_API_URL}/api/admin/refresh`, {
    method: 'POST',
    headers: { 'x-admin-secret': secret, 'Content-Type': 'application/json' },
    body: JSON.stringify(limit ? { limit } : {}),
  });
  if (!res.ok) throw new Error(res.status === 401 ? 'Invalid admin secret' : 'Refresh failed');
  return res.json();
}
