# Deploying to HostingRaja shared hosting (ifscbankfinder.com)

Since this is shared hosting (cPanel + File Manager, no confirmed SSH), you'll do the actual
upload/setup yourself through cPanel and the WordPress admin UI. This doc is the exact sequence.

## 0. Check the PHP version

cPanel → **"MultiPHP Manager"** → find `ifscbankfinder.com` → set PHP to **8.0 or newer** (8.1–8.3
ideal — this plugin was built and tested against 8.3). Older PHP will break on some syntax used
here (typed properties, arrow functions, etc.).

## 1. Install WordPress

If it's not already installed: cPanel → **"WordPress Manager by Softaculous"** → Install, pointing
it at your domain's document root (the `~/ifscbankfinder.com` folder). Use the database you
already have:

- Database name: `itmegrou_ifsc`
- Database user: `itmegrou_ifscban`
- (password you already have — don't need to re-enter it anywhere else)

If WordPress is already installed and just needs to be pointed at that database, its connection
settings live in `wp-config.php` in the site's root folder (edit via cPanel File Manager).

## 2. Set permalinks — do this before anything else breaks

WP Admin → **Settings → Permalinks** → choose **"Post name"** → Save Changes.

This is required. The plugin's custom URLs (`/ifsc/{code}`, `/bank/{slug}`,
`/bank/{slug}/{city}`) depend on WordPress's rewrite system being in "pretty permalinks" mode —
without this step those pages 404.

## 3. Install the theme

WP Admin → **Appearance → Themes → Add New** → search **"Kadence"** → Install → Activate.

(Optional, cosmetic) **Plugins → Add New** → search **"Kadence Blocks"** → Install → Activate —
gives nicer block-editor building blocks for any extra pages you add later. Not required for the
plugin's own pages/shortcodes to work.

## 4. Upload the plugin

WP Admin → **Plugins → Add New → Upload Plugin** → choose the `ifsc-finder.zip` file → Install
Now → **Activate**.

This creates two database tables (`wp_ifsc_branches`, `wp_ifsc_changelog`) automatically on
activation — nothing else to configure manually.

## 5. Create the two pages

WP Admin → **Pages → Add New**, twice:

**Page 1 — "Home"**
- Title: `Home`
- Content (switch to the Code/HTML editor, or a Custom HTML block, and paste exactly this):
  ```
  <h1>Find Any Indian Bank Branch's IFSC Code</h1>
  <p>Search by IFSC code, bank + city, or branch name to get MICR, address, and NEFT/RTGS/IMPS/UPI details.</p>
  <div class="ifsc-layout"><div>[ifsc_search_tabs]</div><aside class="ifsc-sidebar">[ifsc_ad_slot variant=sidebar]</aside></div>[ifsc_popular_banks]
  ```
  Publish.

**Page 2 — "IFSC & MICR Code Guide"**
- Content: `[ifsc_guide]`
- Publish.

> Important: don't let the block editor "smart quotes" your shortcode attributes. If you type
> `variant="sidebar"` directly in a paragraph block, WordPress may convert the straight quotes to
> curly ones and silently break the shortcode. Either use the Code Editor view (top-right "⋮" menu
> → "Code editor") for the Home page content above, or write it as `variant=sidebar` (no quotes at
> all — also valid, and what's shown above).

Then: WP Admin → **Settings → Reading** → "Your homepage displays" → **"A static page"** → Homepage:
**Home**. Save Changes.

## 6. AdSense

WP Admin → **IFSC Finder** (left sidebar menu) → scroll to **AdSense** → paste your client ID
(`ca-pub-8648292919962811`) → **Save AdSense Settings**.

This is the actual point of deploying to a real domain: AdSense won't serve ads on `localhost`,
but will once Google has verified `ifscbankfinder.com`. If you haven't already added/verified
this domain in your AdSense account, do that now (Sites → Add site in the AdSense dashboard) —
the meta tag and script this plugin outputs in `<head>` satisfy the verification requirement.

## 7. Import the data

WP Admin → **IFSC Finder** →

1. Click **"Refresh Now (sample: 500 rows)"** first — fast, proves the import pipeline works on
   this host before committing to the full run.
2. Then click **"Full Import (all records)"** — imports the complete ~182,000-row dataset from
   the same open-source source (github.com/razorpay/ifsc) the Node.js version uses. Takes under a
   minute normally; if it times out, raise PHP's `max_execution_time` in cPanel's **"MultiPHP INI
   Editor"** (try 300) and click it again — the import is idempotent (safe to re-run).

## 8. Verify

- `https://ifscbankfinder.com/` — search works, autosuggest works, Popular Banks links work.
- `https://ifscbankfinder.com/ifsc/SBIN0000001` (or any real code from your data) — detail page,
  check page source has `<meta name="description">` and a `BankOrCreditUnion` JSON-LD block.
- `https://ifscbankfinder.com/wp-sitemap.xml` — lists sitemap chunks including `ifscbranches`.
- `https://ifscbankfinder.com/faq/` — guide content, FAQ accordion, table of contents.
- View source on any page → confirm the AdSense `<meta name="google-adsense-account">` and the
  Auto Ads `<script>` are present in `<head>`.

## Known limitation carried over from local dev

No WP-CLI/SSH here, so the ~15-day scheduled refresh relies on WordPress's own pseudo-cron
(fires on page visits, not wall-clock precise). For something closer to guaranteed, ask
HostingRaja support about a cPanel **"Cron Jobs"** entry hitting
`https://ifscbankfinder.com/wp-cron.php` on a schedule (e.g. daily) — this is the standard
shared-hosting fix for WP-Cron reliability and doesn't need SSH.
