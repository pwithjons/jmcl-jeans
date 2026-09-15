# Production Deployment — VPS or SSH-Enabled Hosting

This is the guide for when the store is ready to go live on real, paid
hosting (a VPS, or shared hosting that gives SSH access) — as opposed
to `DEPLOY.md`, which covers a free, no-SSH demo host (InfinityFree) for
previewing the work-in-progress.

Use this once you're actually launching. The two guides solve different
problems: `DEPLOY.md` = "let me see this on my phone right now, free";
this file = "let's actually sell jeans."

---

## 1. Server requirements

- PHP 8.2 or newer, with these extensions: `mbstring`, `pdo`, `pdo_mysql`
  (or `pdo_sqlite`), `tokenizer`, `xml`, `ctype`, `json`, `bcmath`,
  `fileinfo`, `openssl`, and `gd` (used by `ImageOptimizer` for upload
  compression — see Phase 5 notes in the main README)
- MySQL 8+ (recommended for production) or SQLite (fine for low-traffic
  stores)
- Nginx or Apache
- Composer 2.x, Node.js 18+ (Node is only needed at build time, not on
  the server itself, if you build assets locally/in CI and upload the
  compiled `public/build` folder)
- A domain name with DNS pointed at the server, and a free SSL
  certificate via Let's Encrypt (`certbot`)

## 2. First deployment

```bash
# On the server, as a non-root deploy user:
git clone <your-repo-url> jmcl-jeans
cd jmcl-jeans

composer install --no-dev --optimize-autoloader
npm install && npm run build

cp .env.example .env
php artisan key:generate
# Edit .env: APP_ENV=production, APP_DEBUG=false, APP_URL=https://yourdomain.com,
# real DB credentials, SESSION_SECURE_COOKIE=true, real MAIL_* settings.

php artisan migrate --force
php artisan storage:link

# Cache everything for production performance:
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Laravel needs write access to these two directories:
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Point your web server's document root at the project's `public/`
folder — never at the project root, or `.env`, `app/`, etc. become
downloadable files. A minimal Nginx server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/jmcl-jeans/public;

    add_header X-Frame-Options "SAMEORIGIN";
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Then get a certificate and force HTTPS:
```bash
sudo certbot --nginx -d yourdomain.com
```

## 3. Every deployment after the first

Use `deploy/vps/deploy.sh` in this delivery as a starting point — it
wraps the "pull latest code, install dependencies, migrate, re-cache"
sequence into one script. Read it before running it and adjust
paths/service names for your server.

```bash
bash deploy/vps/deploy.sh
```

## 4. Backups

- **MySQL**: a daily cron running `mysqldump` to a file outside the web
  root, rotated (keep 7-14 days), ideally copied off-server too.
- **SQLite**: back up `database/database.sqlite` the same way — it's
  just a file.
- **Uploaded images**: back up `storage/app/public` alongside the
  database — an order/product record without its photos is only half a
  recovery.

## 5. Ongoing maintenance

- `composer update` / `npm update` periodically, tested locally first —
  never update directly on the production server.
- Watch `storage/logs/laravel.log` for repeated errors.
- The seeded demo accounts and demo orders are for development only —
  see the checklist below.

---

## Pre-Launch Checklist

Run through this once, right before pointing the domain at this app for
real customers:

- [ ] Changed both seeded admin passwords (`superadmin@jmcljeans.test`,
      `admin@jmcljeans.test`) — or deleted those accounts and created
      real ones
- [ ] Ran migrations WITHOUT seeding demo data in production
      (`php artisan migrate --force`, no `--seed`) — the demo customer,
      demo orders, and placeholder product images are for local/preview
      use only
- [ ] `APP_ENV=production` and `APP_DEBUG=false` — a debug page in
      production leaks stack traces, file paths, and env values
- [ ] A real `APP_KEY` generated for this deployment specifically
      (never reuse a key from a dev environment)
- [ ] HTTPS is live and `SESSION_SECURE_COOKIE=true` is set
- [ ] Real store contact details entered (store email/phone/address —
      seeded via `SettingSeeder` in dev; set real values via
      `Setting::set(...)` or a future admin Settings screen)
- [ ] Shipping charges in admin **Shipping** reflect real delivery
      costs, not the seeded placeholder values
- [ ] Reviewed which coupons (if any) should actually be live
- [ ] Backups configured and *tested* (restore, don't just take one)
- [ ] `storage/logs/laravel.log` is being monitored somehow
- [ ] Either checking the contact form's logged messages regularly, or
      real `MAIL_*` settings are wired up so they arrive by email
- [ ] Any leftover one-time scripts from a prior free-tier demo
      deployment (see `DEPLOY.md`) have been deleted from that host
