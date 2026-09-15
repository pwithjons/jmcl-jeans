<?php
/**
 * ONE-TIME USE ONLY. Delete this file from the server immediately after
 * running it once successfully.
 *
 * InfinityFree (and most no-SSH shared hosts) give no terminal access, so
 * `php artisan migrate --seed` can't be run the normal way. This script
 * bootstraps Laravel just enough to call the same Artisan commands over
 * HTTP, one visit, one time.
 *
 * Usage:
 *   1. Upload this file into the SAME folder as your uploaded
 *      `artisan` file (i.e. the laravel_app folder — see DEPLOY.md).
 *   2. Visit https://yoursubdomain.infinityfreeapp.com/laravel_app/migrate.php
 *      (adjust the path to wherever you placed it)
 *   3. Confirm it prints "Done." with no errors.
 *   4. DELETE this file via FTP immediately. Leaving it live lets anyone
 *      who finds the URL wipe or reseed your database.
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

echo "Running migrations...\n";
$exitCode = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();

echo "\nSeeding demo data...\n";
Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();

echo "\nDone. DELETE THIS FILE NOW.\n";
