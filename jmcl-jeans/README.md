# JMCL JEANS LTD — E-commerce Platform

Built with Laravel + Blade + Tailwind CSS, using only free/open-source technology.

## ✅ Project Complete — All 6 Phases Delivered

This delivery finishes **Phase 6 (Deployment Packaging)** — the last
phase. The full build (database → auth → admin panel → storefront →
hardening → deployment) is done. See "Roadmap" near the bottom for the
phase-by-phase history, and the recap sections below for what each
phase actually built.

**Two deployment guides, for two different needs:**
- **`DEPLOY.md`** — free, no-credit-card, no-SSH demo hosting
  (InfinityFree). Good for showing this on a phone right now.
- **`PRODUCTION.md`** — real launch on paid hosting (VPS or SSH-enabled
  shared hosting), with a pre-launch checklist, backup guidance, and a
  `deploy/vps/deploy.sh` script for repeat deploys.

## ⚠️ Phase 6 of 6 — COMPLETE — Deployment Packaging

**Phase 6 (this delivery) adds:**
- **`PRODUCTION.md`** — server requirements, first-deploy steps, a
  sample Nginx config, backup strategy, and a pre-launch checklist
  (change default passwords, disable debug mode, confirm HTTPS, etc.)
- **`deploy/vps/deploy.sh`** — a repeatable one-command deploy script
  for after the first setup (pull → install → build → migrate → cache
  → restart PHP-FPM)
- Confirmed everything the original spec asked for under "Deployment"
  is present: `.env.example`, migrations, seeders, storage
  configuration (`storage:link` documented in both deploy guides),
  production configuration guidance, this README, and installation
  instructions

## Phase 5 recap — SEO, Performance & Security Hardening

Phase 5 didn't add new pages — it hardened everything built in Phases
1–4. No new database tables or migrations.

**SEO:**
- `/sitemap.xml` — auto-generated from live published products, active
  categories, and the static pages; cached 1 hour (and invalidated
  immediately when a category is created/edited/deleted)
- `/robots.txt` — blocks `/admin`, `/cart`, `/checkout`, `/account`,
  `/profile` from crawlers, points to the sitemap
- Open Graph tags (title/description/image) on every page, so shared
  product links show a preview image and correct title
- Canonical URL tag on every page
- Alt-text fallback to the product/category name wherever an image
  might otherwise render with an empty `alt`

**Performance:**
- **New `ImageOptimizer` service** — every product/category image
  upload now goes through it: resized down to a 1600px max width and
  re-encoded as JPEG at 78% quality (via Intervention Image, already in
  `composer.json`). Without this, an admin uploading a 6000px phone
  photo would ship that full file to every visitor.
- Category list (used in every page's nav bar) is cached for 5 minutes
  instead of queried on every request, with automatic cache invalidation
  the instant a category is saved or deleted — so admin changes are
  never stuck behind a stale cache
- `loading="lazy"` added to product-grid and category-tile images
  (hero images stay eager since they're above the fold)

**Security:**
- **Rate limiting** added to every form an anonymous visitor can submit
  repeatedly: contact form (5/min), coupon apply (10/min), cart
  add/update (30/min), checkout (10/min), plus a coarse IP-based limit
  on login/register (10/min) on top of the per-email lockout already in
  place since Phase 2
- **New `SecurityHeaders` middleware** (applied globally): adds
  `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, and
  `Permissions-Policy` to every response. A strict Content-Security-
  Policy was deliberately left out — see the class docblock for why
  (a few small inline Alpine `<script>` blocks would need moving to
  external files first, which is a Phase 6 task)
- **Forced HTTPS URL generation in production** — matters especially
  for the InfinityFree-style hosting in `DEPLOY.md`, where the app sits
  behind a proxy that terminates HTTPS
- Manual review confirmed: no raw/unescaped Blade output (`{!! !!}`)
  anywhere, no raw SQL with interpolated user input, every model has an
  explicit `$fillable` list (no accidental mass-assignment surface)
- `.env.example` documents `SESSION_SECURE_COOKIE` for HTTPS production
  use

---

## Phase 1–4 recap

**Phase 4 (storefront), in full:**
- **4c — Customer Account**: order history, visual order tracking,
  ownership enforced server-side
- **4b — Cart & Checkout**: guest + account cart with merge-on-login,
  coupons, delivery-zone shipping, atomic stock re-verification
- **4a — Storefront browsing**: Home, Shop, Product detail, Category,
  Search, static pages; denim/charcoal/gold design direction

**Phase 3 (admin panel), in full:**

**Phase 3a — Products & Categories:**
- Admin **Categories** & **Subcategories** CRUD (image, SEO fields,
  active/inactive) — blocked from deleting one that still has products
- Admin **Products** CRUD: category/subcategory selection, dynamic
  size/color **variations** (add/remove rows in-browser, own SKU/stock/
  price override each), multiple image upload with primary/delete
  controls, publish/unpublish toggle, search + filter
- `ProductService` holds the variation-sync and image logic so the
  controller stays thin

**Phase 3b — Orders & Customers:**
- Admin **Orders**: status tabs, search, date-range filter, order detail
  page, status-update form
- **Cancelling an order automatically restocks its items** (`OrderService`)
- Admin **Customers**: list with search + spend totals, detail page with
  addresses and order history

**Phase 3c (this delivery) — Coupons, Shipping, Reports, real Dashboard:**
- Admin **Coupons** CRUD: percentage or fixed discount, minimum order
  amount, usage limit, expiry date, active toggle (code auto-uppercased)
- Admin **Shipping** settings: inside/outside-city charges, optional free
  delivery above a minimum order amount (single settings row — no
  list/create/delete needed for this one)
- Admin **Reports**: date-range sales summary (total sales, order count,
  average order value), sales-by-day table, top 5 products by units sold
- **Dashboard now shows real numbers**: total products/categories/
  customers/orders, orders broken down by status, total sales, 5 most
  recent orders, and a low-stock alert (≤5 units) list

Note: "Inventory" from the original spec is handled inside the Products
screen (per-variation stock, editable there) rather than as a separate
page — there was no additional inventory-only workflow to justify a
second screen for the same data.

### One-time setup step

Uploaded category/product images are stored on Laravel's `public` disk.
After `composer install`, link it so uploaded files are web-accessible:
```bash
php artisan storage:link
```

## How to install this on your machine

These files are an **overlay**, not a full Laravel skeleton (the framework's
own boilerplate — `bootstrap/`, `public/index.php`, `artisan`, base `config/`
files — isn't included here to keep this delivery focused on JMCL-specific
code). To run it:

```bash
# 1. Create a fresh Laravel 11 project
composer create-project laravel/laravel jmcl-jeans
cd jmcl-jeans

# 2. Copy the contents of this delivery into it, overwriting where prompted:
#    - app/Models/*
#    - app/Http/Controllers/*
#    - app/Http/Requests/*
#    - app/Http/Middleware/EnsureAdminRole.php
#    - app/Services/*
#    - app/Providers/AppServiceProvider.php
#    - bootstrap/app.php
#    - routes/web.php, routes/auth.php, routes/admin.php, routes/console.php
#    - database/migrations/* (the 0001_01_01_* files replace the defaults)
#    - database/seeders/*
#    - config/auth.php
#    - resources/views/* (layouts, auth, profile, home.blade.php, admin/*)
#    - resources/css/app.css, resources/js/app.js
#    - composer.json, package.json, tailwind.config.js, vite.config.js
#    - .env.example

# 3. Install dependencies
composer install
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Database (SQLite — zero config)
touch database/database.sqlite
php artisan migrate --seed

# 6. Build frontend assets
npm run build
# or for development:
npm run dev

# 7. Link the storage disk (needed for uploaded category/product images)
php artisan storage:link

# 8. Serve the app
php artisan serve
```

Visit `http://localhost:8000`.

### Default seeded accounts

| Role | Login URL | Email | Password |
|---|---|---|---|
| Super Admin | `/admin/login` | superadmin@jmcljeans.test | password |
| Admin | `/admin/login` | admin@jmcljeans.test | password |
| Customer | `/login` | (register your own at `/register`) | — |

**Change these passwords before any real deployment.**

### Reviewing Phase 6

There's nothing to click through in the app itself for this phase — it's
documentation and a deploy script, not new pages. To review it:

1. Read `PRODUCTION.md` top to bottom — does the pre-launch checklist
   make sense for how you plan to actually launch?
2. Open `deploy/vps/deploy.sh` — if you're deploying to a VPS later,
   adjust the PHP-FPM service name near the bottom for your server
   before you'll ever need to run it.
3. Confirm you're comfortable with the two-guide split (`DEPLOY.md` for
   free demo hosting now, `PRODUCTION.md` for the real launch later) —
   say so if you'd rather have just one combined guide instead.

### Trying out Phase 5

1. Visit `/sitemap.xml` and `/robots.txt` directly — confirm both render
   and the sitemap lists your seeded products/categories.
2. Share a product link (or check its page source) for the `og:` meta
   tags and confirm `og:image` points at that product's photo.
3. Submit the contact form 6 times in under a minute — the 6th should be
   rate-limited (HTTP 429 / "Too Many Requests").
4. In the admin panel, deactivate a category, then reload the storefront
   — it should disappear from the nav immediately (not wait 5 minutes).
5. Upload a large product image (several MB) in admin **Products** →
   confirm the stored file is resized/compressed (check its file size in
   `storage/app/public/products`).
6. Open browser dev tools → Network tab → reload any storefront page →
   confirm response headers include `X-Frame-Options` and
   `X-Content-Type-Options`.

### Trying out Phase 4c

1. Log in as a customer who has placed at least one order (or place a
   new one via Checkout first).
2. Go to `/profile` → click the **My Orders** tab → confirm your orders
   list appears with status badges.
3. Open an order → confirm the step tracker shows the right stage.
4. In the admin panel, open that same order under **Orders** and change
   its status (e.g. pending → processing) → refresh the customer-side
   order page → the tracker should reflect the new status immediately
   (both read from the same `order_status` field).
5. Try visiting another customer's order URL directly (e.g. change the
   ID in the address bar) — you should get a 404, not their order.

### Trying out Phase 4b

1. As a guest (not logged in), open a product → select size/color → Add
   to Cart → go to `/cart` → confirm it shows.
2. Apply coupon `WELCOME10` (seeded, needs ৳1000+ subtotal) or `FLAT200`
   (needs ৳2000+) on the Cart page.
3. Register a new account while that guest cart still has items → after
   registering, check `/cart` again — the item should still be there
   (merged into your new account).
4. Go to Checkout → pick "Inside City" or "Outside City" → Place Order →
   confirm the delivery charge matches what's configured in
   admin **Shipping** settings, and the coupon discount carried over.
5. In the admin panel, check **Orders** — the new order should appear
   with `pending` status, and **Products** → the purchased variation's
   stock should have decreased by the ordered quantity.

### Trying out Phase 4a

1. Visit `/` — the homepage should show real seeded products (featured,
   new arrivals) and real category images.
2. Go to **Shop** → filter by size or color → change sort to "Price:
   Low to High" → confirm the URL carries the filters (shareable link).
3. Open any product → click through the size/color buttons → price and
   stock message should update instantly without a page reload.
4. Visit `/about`, `/contact` (submit the form — check `storage/logs/laravel.log`
   for the logged message), `/privacy-policy`, `/terms-and-conditions`.

### Trying out Phase 3c

1. Go to **Dashboard** — should now show real counts and a low-stock
   list (WELCOME10 / FLAT200 coupons and the 3 demo orders seeded
   earlier already give it something to count).
2. Go to **Coupons** → edit `WELCOME10` → note the code field
   auto-uppercases.
3. Go to **Shipping** → toggle "Enable free delivery" → the minimum-
   amount field should show/hide accordingly → save and confirm it
   persists.
4. Go to **Reports** → change the date range to cover today → confirm
   the 3 demo orders show up in the daily breakdown and top-products list.

### Trying out Phase 3b

`php artisan migrate --seed` also seeds 3 demo orders (delivered,
processing, pending) against a demo customer.

| Demo customer | Email | Password |
|---|---|---|
| Rahim Ahmed | demo.customer@jmcljeans.test | password |

1. Go to **Orders** → use the status tabs to filter → open the
   "processing" order → change its status to **Cancelled** → check the
   product's variation stock in **Products** increased back by the
   cancelled quantity.
2. Go to **Customers** → search "Rahim" → open the profile to see the
   saved address and order history.

### Trying out Phase 3a

1. Go to **Categories** → edit "Jeans" → change its status or image.
2. Go to **Products** → **+ New Product** → fill in the basics, add a
   couple of size/color variation rows, upload an image or two, save.
3. From the product list, click the status text to toggle
   published/unpublished without opening the edit form.
4. Try deleting a category that still has products assigned — it should
   refuse with a message instead of breaking.

### Switching from SQLite to MySQL later

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jmcl_jeans
DB_USERNAME=root
DB_PASSWORD=
```
Then re-run `php artisan migrate --seed`. No code changes needed — the
migrations and models are database-agnostic.

## Design notes for reviewers

- **Storefront visual direction (Phase 4a):** the hero uses an
  asymmetric split with a staggered 3-image cluster instead of a
  centered banner, category tiles are flat full-bleed images with a
  bottom-left caption (no shadow/rounded-card treatment), and the
  "Why JMCL" section uses plain text blocks separated by a hairline
  rule instead of icon cards — deliberately avoiding the generic
  SaaS-card and icon-grid look for a fashion-retail feel instead.
- **Soft deletes on products** — so a discontinued product doesn't break
  historical order records.
- **Order snapshots customer + item data** — an order keeps its own copy of
  name/address/price/product-name, so edits to a customer profile or product
  catalog never rewrite order history.
- **`payment_method` is a plain string**, not an enum — adding bKash, Nagad,
  or SSLCommerz later is a matter of registering a new gateway class, not a
  schema migration.
- **Separate `admins` table + `admin` auth guard** — keeps customer and
  admin sessions completely isolated (see `config/auth.php`).
- **Per-variation stock** — `product_variations` tracks stock per
  size/color combination, not just per product.
- **Placeholder images** use [placehold.co](https://placehold.co) (free, no
  API key) — swap for real product photography before launch.

## Roadmap

- [x] Phase 1 — Database & models
- [x] Phase 2 — Authentication (customer + admin, role-based access)
- [x] Phase 3a — Admin panel: Products & Categories
- [x] Phase 3b — Admin panel: Orders & Customers
- [x] Phase 3c — Admin panel: Coupons, Shipping, Reports, real Dashboard stats
- [x] Phase 4a — Storefront: Home, Shop, Product page, Category, Search, static pages
- [x] Phase 4b — Storefront: Cart & Checkout
- [x] Phase 4c — Storefront: Customer Account (order history/tracking)
- [x] Phase 5 — SEO, responsive polish, performance, security hardening — this delivery
- [x] Phase 6 — Deployment packaging — this delivery

**All phases complete.**
