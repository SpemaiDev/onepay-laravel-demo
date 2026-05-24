#!/bin/bash
# Azure App Service startup script for Laravel (SQLite)
# Set this as the Startup Command in App Service → Configuration → General settings

set -e

APP_DIR=/home/site/wwwroot

# ── Persistent SQLite storage ─────────────────────────────────────────────────
# /home/data persists across deploys and restarts.
# /home/site/wwwroot is wiped on every deploy, so DO NOT store the DB there.
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

# ── Storage / cache permissions ───────────────────────────────────────────────
echo "==> Setting storage permissions..."
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

# ── Refresh caches with real Azure App Settings ───────────────────────────────
# The workflow bakes caches from .env.example; re-run here with live env values.
echo "==> Refreshing Laravel caches..."
php "$APP_DIR/artisan" config:cache
php "$APP_DIR/artisan" route:cache
php "$APP_DIR/artisan" view:cache

# ── Run migrations ────────────────────────────────────────────────────────────
echo "==> Running database migrations..."
php "$APP_DIR/artisan" migrate --force

echo "==> Startup complete."

# Hand off to Azure's default PHP/nginx startup
/usr/local/bin/startup.sh
