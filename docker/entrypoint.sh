#!/bin/sh
set -e

echo "==> Starting HR Manager..."

# Create .env from environment variables if not exists
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creating .env from environment variables..."
    cp /var/www/html/.env.example /var/www/html/.env

    # Override .env values with actual environment variables from Render
    [ -n "$APP_NAME" ]       && sed -i "s|^APP_NAME=.*|APP_NAME=${APP_NAME}|" .env
    [ -n "$APP_ENV" ]        && sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
    [ -n "$APP_KEY" ]        && sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
    [ -n "$APP_DEBUG" ]      && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env
    [ -n "$APP_URL" ]        && sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
    [ -n "$DB_CONNECTION" ]  && sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" .env
    [ -n "$DB_HOST" ]        && sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST}|" .env
    [ -n "$DB_PORT" ]        && sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" .env
    [ -n "$DB_DATABASE" ]    && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env
    [ -n "$DB_USERNAME" ]    && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env
    [ -n "$DB_PASSWORD" ]    && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env
    [ -n "$MAIL_HOST" ]      && sed -i "s|^MAIL_HOST=.*|MAIL_HOST=${MAIL_HOST}|" .env
    [ -n "$MAIL_PORT" ]      && sed -i "s|^MAIL_PORT=.*|MAIL_PORT=${MAIL_PORT}|" .env
    [ -n "$MAIL_USERNAME" ]  && sed -i "s|^MAIL_USERNAME=.*|MAIL_USERNAME=${MAIL_USERNAME}|" .env
    [ -n "$MAIL_PASSWORD" ]  && sed -i "s|^MAIL_PASSWORD=.*|MAIL_PASSWORD=${MAIL_PASSWORD}|" .env
    [ -n "$MAIL_ENCRYPTION" ] && sed -i "s|^MAIL_ENCRYPTION=.*|MAIL_ENCRYPTION=${MAIL_ENCRYPTION}|" .env
    [ -n "$MAIL_FROM_ADDRESS" ] && sed -i "s|^MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}|" .env

    echo "==> .env file created successfully."
fi

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

# Generate APP_KEY if still empty
cd /var/www/html
APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "" ]; then
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

