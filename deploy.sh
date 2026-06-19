#!/bin/bash
set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_CONTAINER="superposko-app"

echo "🚀 Deploying Superposko..."

# 1. Fix permissions
echo "🔑 Fixing permissions..."
chown -R 1000:1000 "$APP_DIR"

# 2. Pull latest code
echo "📦 Pulling latest code..."
git -C "$APP_DIR" fetch origin
git -C "$APP_DIR" reset --hard origin/main

# 3. Fix permissions again after pull
chown -R 1000:1000 "$APP_DIR"

# 4. Install PHP dependencies
echo "🎻 Installing Composer dependencies..."
docker exec "$APP_CONTAINER" composer install --no-dev --optimize-autoloader --no-interaction

# 5. Build frontend
echo "🏗️  Building frontend assets..."
docker exec "$APP_CONTAINER" npm ci --prefer-offline
docker exec "$APP_CONTAINER" npm run build

# 6. Run migrations
echo "🗄️  Running migrations..."
docker exec "$APP_CONTAINER" php artisan migrate --force

# 7. Clear & cache config
echo "⚡ Caching config..."
docker exec "$APP_CONTAINER" php artisan config:cache
docker exec "$APP_CONTAINER" php artisan route:cache
docker exec "$APP_CONTAINER" php artisan view:cache

echo "✅ Deploy selesai!"
