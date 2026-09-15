# Deploying JMCL JEANS LTD (demo) to InfinityFree — Free, No Credit Card

This gets what's been built so far (Phases 1–4a: full admin panel + the
browsing storefront — no cart/checkout yet) onto a real public URL you
can view from your phone. It's for **demo/preview purposes** — InfinityFree
has no SSH access, no cron jobs, and a 30-second PHP execution limit, so
it's not meant for real production traffic once the store is finished.

You'll need **one session on a computer** with PHP, Composer, and Node.js
installed to prepare the files. After that, everything is viewable from
your phone's browser.

---

## Step 1 — Sign up (5 minutes, on any device)

1. Go to infinityfree.com and create a free account (no credit card).
2. Create a new hosting account — pick a free subdomain, e.g.
   `jmcljeans.infinityfreeapp.com`.
3. Wait for account activation (usually a few minutes), then open the
   **Control Panel (VistaPanel)**.
4. Under **MySQL Databases**, create a new database. Note down:
   - Database host (something like `sqlXXX.infinityfree.com`)
   - Database name
   - Database username
   - Database password
5. Under **FTP Accounts**, note your FTP host, username, and password
   (or use the one auto-created for your account).

## Step 2 — Prepare the app locally (on the computer)

```bash
# Extract this delivery's zip, then inside the jmcl-jeans folder:
composer create-project laravel/laravel jmcl-jeans-build
cd jmcl-jeans-build

# Copy this delivery's files into it (see README.md's "How to install"
# section for the exact file list), then:

composer install --no-dev --optimize-autoloader
npm install
npm run build

cp .env.example .env
php artisan key:generate
```

Now edit `.env` for production:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jmcljeans.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=<your db name from Step 1>
DB_USERNAME=<your db username from Step 1>
DB_PASSWORD=<your db password from Step 1>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

## Step 3 — Upload via FTP

InfinityFree only gives you FTP access inside a `htdocs/` folder, so the
Laravel app has to be split:

1. Using an FTP client (FileZilla is free), connect with the FTP details
   from Step 1.
2. Inside `htdocs/`, create a folder called `laravel_app`.
3. Upload **everything except the `public/` folder's contents** into
   `htdocs/laravel_app/` — that means `app/`, `bootstrap/`, `config/`,
   `database/`, `resources/`, `routes/`, `storage/`, `vendor/`, `artisan`,
   `composer.json`, `.env`, etc.
4. Upload the **contents of `public/`** (not the `public` folder itself —
   its *contents*) directly into `htdocs/`. So `htdocs/index.php`,
   `htdocs/build/`, `htdocs/.htaccess` etc.
5. Edit `htdocs/index.php` (open it in any text editor, even on FTP) and
   change the two `require` lines as shown in
   `deploy/infinityfree/index.php.reference` in this delivery — point
   them at `laravel_app/vendor/...` and `laravel_app/bootstrap/...`.
6. Upload `deploy/infinityfree/.htaccess-deny-all` into
   `htdocs/laravel_app/` and rename it to `.htaccess` there — this blocks
   anyone from directly browsing to your app code or `.env` file.

## Step 4 — Run migrations (no SSH workaround)

1. Upload `deploy/infinityfree/migrate.php` into `htdocs/laravel_app/`.
2. Visit `https://jmcljeans.infinityfreeapp.com/laravel_app/migrate.php`
   in your browser (phone is fine for this part).
3. You should see "Running migrations...", then "Seeding demo data...",
   then "Done. DELETE THIS FILE NOW."
4. **Immediately delete `migrate.php`** via FTP. This step is not
   optional — leaving it live means anyone who finds the URL can wipe or
   reseed your database.

## Step 5 — Product images

Admin-uploaded images (via the Products/Categories admin screens) are
saved to `storage/app/public`, which normally needs
`php artisan storage:link` — a command that needs SSH, which InfinityFree
doesn't give you. For this demo, the simplest fix is to **skip uploading
your own images for now** and rely on the seeded placeholder images
(they're external `placehold.co` URLs and will display fine regardless).
Real image uploads can wait until this moves to proper hosting in
Phase 6.

## Step 6 — Visit the site

Open `https://jmcljeans.infinityfreeapp.com` on your phone. You should
see the full storefront home page. Admin panel is at `/admin/login`
(same seeded credentials as local: see main README.md).

---

## Known limitations of this demo deployment

- Image uploads are resized/compressed with the GD PHP extension
  (via Intervention Image) — InfinityFree has GD enabled by default, but
  if uploads fail after Step 5, check the control panel's PHP extension
  list for `gd`.

- No SSH means no easy way to re-run migrations later if the schema
  changes in a future phase — you'd repeat Step 4 with a fresh
  `migrate.php` upload (and delete it again after).
- 30-second PHP execution limit and shared, sometimes-slow servers — fine
  for showing someone the design and admin panel, not for real customers.
- Admin-uploaded images won't display (see Step 5) — seeded placeholder
  images work fine.
- This checklist should be re-run (or updated) once Phase 4b/4c/5/6 add
  more code — at minimum, re-upload changed files and re-run any new
  migrations via a fresh one-time `migrate.php`.
