#!/bin/bash
# Azure App Service startup script for Laravel (SQLite)
# Set as Startup Command: /home/site/wwwroot/startup.sh

set -e

APP_DIR=/home/site/wwwroot

echo "==> Creating required Laravel directories..."
mkdir -p \
  "$APP_DIR/storage/framework/views" \
  "$APP_DIR/storage/framework/cache/data" \
  "$APP_DIR/storage/framework/sessions" \
  "$APP_DIR/storage/logs" \
  "$APP_DIR/bootstrap/cache"

# ── Persistent SQLite storage ─────────────────────────────────────────────────
# /home/data persists across deploys. /home/site/wwwroot is wiped on every deploy.
SQLITE_DIR=/home/data/database
SQLITE_FILE="$SQLITE_DIR/database.sqlite"

echo "==> Ensuring persistent SQLite directory..."
mkdir -p "$SQLITE_DIR"

if [ ! -f "$SQLITE_FILE" ]; then
    echo "==> Creating fresh SQLite database at $SQLITE_FILE..."
    touch "$SQLITE_FILE"
fi

chmod 664 "$SQLITE_FILE"
chmod 775 "$SQLITE_DIR"

echo "==> Setting storage permissions..."
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
chmod -R 755 "$APP_DIR/public" 2>/dev/null || true

# Remove Azure default placeholder page if present
rm -f "$APP_DIR/hostingstart.html" 2>/dev/null || true

# ── Configure nginx for Laravel (serve from /public) ─────────────────────────
echo "==> Applying nginx configuration for Laravel..."
NGINX_TARGET="/etc/nginx/sites-enabled/default"
if [ -f "$APP_DIR/nginx.conf" ]; then
    cp "$APP_DIR/nginx.conf" "$NGINX_TARGET"
    nginx -t && service nginx reload
    echo "==> nginx reloaded with Laravel public root."
else
    echo "==> WARNING: nginx.conf not found, skipping nginx configuration."
fi

# ── Refresh caches with real Azure App Settings ───────────────────────────────
echo "==> Clearing stale caches..."
php "$APP_DIR/artisan" config:clear || true
php "$APP_DIR/artisan" route:clear || true
php "$APP_DIR/artisan" view:clear || true

echo "==> Building Laravel caches..."
php "$APP_DIR/artisan" config:cache
php "$APP_DIR/artisan" route:cache
php "$APP_DIR/artisan" view:cache

echo "==> Running database migrations..."
php "$APP_DIR/artisan" migrate --force

echo "==> Startup complete."
