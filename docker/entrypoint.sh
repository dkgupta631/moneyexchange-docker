#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Populate shared public volume from image on first run.
# Skip if index.php already exists (bind-mount dev mode or re-run).
if [ ! -f /var/www/html/public/index.php ]; then
    echo "Initializing public directory..."
    cp -r /public_init/. /var/www/html/public/
fi

# Run composer post-install scripts now that DB is available
php artisan package:discover --ansi || true
php artisan filament:upgrade || true

# Create storage symlink
php artisan storage:link --force || true

# Run migrations
php artisan migrate --force

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
