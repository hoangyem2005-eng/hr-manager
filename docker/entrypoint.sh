#!/bin/sh
set -e

echo "==> Starting HR Manager..."

# Create storage directories if not exist
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force || echo "Migration failed or already up to date"

# Cache configs
echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
