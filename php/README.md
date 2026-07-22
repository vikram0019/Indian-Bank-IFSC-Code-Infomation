# IFSC Finder — WordPress Port

A PHP/WordPress port of the Node/Next.js IFSC Finder (see the repo root README). Same product:
search Indian bank IFSC codes by code, bank+city, or branch name, with SEO-friendly pages, a
guide/FAQ page, and an admin refresh dashboard — implemented as a custom WordPress plugin
(`wp-content/plugins/ifsc-finder`) plus the free **Kadence** theme (a Divi alternative — Divi is
a paid Elegant Themes product we can't distribute) for global page chrome.

WordPress's default post/postmeta model doesn't scale to prefix-search across ~182k rows, so
this is a real port: a dedicated MySQL table (`wp_ifsc_branches`), a REST API under
`wp-json/ifsc/v1/...`, custom rewrite rules for pretty URLs, and a WP-CLI import command —
not just "install WordPress and a plugin."

## Prerequisites

Docker Desktop. No local PHP/MySQL/wp-cli needed — everything runs in containers.

## Setup

```bash
cd php
cp .env.example .env    # edit passwords if you like
docker compose up -d
```

Install WordPress core (first time only):

```bash
docker compose run --rm wpcli core install \
  --url=http://localhost:8080 \
  --title="IFSC Finder" \
  --admin_user=admin \
  --admin_password=<choose one> \
  --admin_email=you@example.com \
  --skip-email

docker compose run --rm wpcli rewrite structure '/%postname%/' --hard
docker compose run --rm wpcli plugin activate ifsc-finder
docker compose run --rm wpcli theme install kadence --activate
docker compose run --rm wpcli plugin install kadence-blocks --activate
```

Create the two pages the plugin's shortcodes live on (skip if already created):

```bash
docker compose run --rm wpcli post create --post_type=page --post_title="Home" \
  --post_name="home" --post_status=publish \
  --post_content='[ifsc_search_tabs][ifsc_popular_banks]' --porcelain

docker compose run --rm wpcli post create --post_type=page --post_title="IFSC & MICR Code Guide" \
  --post_name="faq" --post_status=publish --post_content='[ifsc_guide]' --porcelain

docker compose run --rm wpcli option update show_on_front page
docker compose run --rm wpcli option update page_on_front <home-page-id>
```

Import data (same razorpay/ifsc CSV source as the Node app):

```bash
docker compose run --rm wpcli ifsc import --limit=500   # fast local sample
docker compose run --rm wpcli ifsc import                # full ~182k rows (~20s)
```

Site: http://localhost:8080 · phpMyAdmin: http://localhost:8081 · wp-admin: http://localhost:8080/wp-admin

## Notable deviations from the Node/Next version

- **Admin auth**: uses WordPress's own `current_user_can('manage_options')` instead of the
  Node app's shared-secret header hack (deliberate improvement).
- **Sitemap**: registered as a `WP_Sitemaps_Provider`, auto-listed at `/wp-sitemap.xml` — note
  the provider is named `ifscbranches` (no hyphen). WordPress core's rewrite regex for
  single-segment sitemap URLs (`^wp-sitemap-([a-z]+?)-(\d+?)\.xml$`) only matches plain
  letters; a hyphenated name gets misparsed as `{provider}-{subtype}`.
- **wp-cli image gotcha**: `wordpress:cli-php8.3` is Alpine-based (`www-data`=uid 82) while
  `wordpress:*-apache` is Debian-based (`www-data`=uid 33). The `wpcli` service pins
  `user: "33:33"` in `docker-compose.yml` so file writes into the shared volume/bind mounts
  don't fail with permission errors.

## Known shared limitation (also present in the Node/Next version)

Bank names containing a literal hyphen (e.g. "XYZ Co-operative Bank") don't round-trip cleanly
through the URL slugify/unslugify (`replace(/-/g, ' ')` turns every dash to a space, including
ones that were part of the name), so a direct link to such a bank's `/bank/{slug}` page 404s.
This affects none of the 20 "Popular Banks" links (no hyphens in those names) — only bank pages
reached some other way. Same root cause in both apps; not something this port introduced.

## Deferred (matches the Node app's own deferrals)

Redis-equivalent caching, real Google AdSense keys, actual VPS/live deployment, multi-language,
automated tests/CI.
