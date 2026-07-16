# Indian Bank IFSC Finder

A full-stack search engine for Indian bank IFSC codes and branch details (bank, branch, city,
state, MICR, contact, NEFT/RTGS/IMPS/UPI availability), with SEO-friendly pages, an admin
dashboard, and automated data refresh. See [project-planning/scope.md](project-planning/scope.md)
for the full product scope.

## Monorepo layout

npm workspaces with two apps:

- **`server/`** — Node.js + Express + MongoDB (Mongoose) REST API
- **`client/`** — Next.js (App Router) + Tailwind CSS frontend

## Prerequisites

- Node.js 20+
- MongoDB running locally (`mongodb://localhost:27017`) or reachable via `MONGODB_URI`

## Setup

```bash
npm install                 # installs both workspaces
cp server/.env.example server/.env
cp client/.env.example client/.env.local
```

Seed the database from the open-source [razorpay/ifsc](https://github.com/razorpay/ifsc) dataset
(~182k branch records, community-maintained, derived from RBI data — RBI does not publish an
official machine-readable file):

```bash
npm run seed                       # full ~182k-row import (several minutes)
npm run seed -- --limit 500        # fast partial import for local dev/testing
```

Run both apps:

```bash
npm run dev:server   # http://localhost:4000
npm run dev:client   # http://localhost:3000
```

## API (server)

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/health` | Health check |
| GET | `/api/ifsc/:code` | Exact IFSC lookup |
| GET | `/api/search?bank=&city=` | Branches for a bank in a city |
| GET | `/api/branch/search?name=` | Branches matching a branch name |
| GET | `/api/suggest?q=&type=` | Autosuggest (`type`: `ifsc`\|`bank`\|`branch`) |
| GET | `/api/sitemap-data?cursor=&limit=` | Paginated feed for the sitemap generator |
| POST | `/api/admin/refresh` | Trigger a data refresh (protected) |
| GET | `/api/admin/stats` | Dataset stats + change log (protected) |

Admin routes require an `x-admin-secret` header matching `ADMIN_SECRET` in `server/.env`.

## Frontend (client)

- `/` — search (by IFSC, bank+city, or branch name) with autosuggest, plus FAQ
- `/ifsc/[code]` — branch detail page with per-page SEO metadata and `BankOrCreditUnion` JSON-LD
- `/bank/[bankcode]/[city]` — branch listing for a bank in a city
- `/faq` — FAQ page with `FAQPage` JSON-LD
- `/admin` — password-gated dashboard (dataset stats, change log, manual refresh)
- `/sitemap/[id].xml`, `/robots.txt` — auto-generated from the live dataset

## Data refresh ("cron job")

`server/scripts/refreshData.js` downloads the latest razorpay/ifsc CSV, upserts it into MongoDB,
and writes a `ChangeLog` entry (added/removed/modified counts). It's wired into `node-cron`
(`server/src/cron/refreshScheduler.js`) to run automatically on an approximate 15-day schedule,
and can also be triggered manually from `/admin`. For a real production deployment, prefer Linux
crontab on the VPS over the in-process scheduler.

## Explicitly deferred (not built yet)

Redis caching, real production cron scheduling, Google AdSense integration (placeholder ad slots
exist), affiliate/sponsored content, actual VPS/AWS/Vercel deployment, multi-language support,
maps/holiday-calendar/customer-care features. See
[project-planning/progress.md](project-planning/progress.md) for the current status.

## Migration note

The original flat scaffold (root `src/server.js`, `src/data/ifsc-data.json`) has been removed and
replaced by the `server/` + `client/` monorepo described above.
