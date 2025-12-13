#!/bin/bash

echo "========================================"
echo "  Starting Laravel Application"
echo "========================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "[INFO] Creating .env file..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "[ERROR] .env.example not found. Please create .env manually."
        exit 1
    fi
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "[INFO] Generating application key..."
    php artisan key:generate
fi

# Check if database.sqlite exists
if [ ! -f database/database.sqlite ]; then
    echo "[INFO] Creating database file..."
    touch database/database.sqlite
fi

# Run migrations
echo "[INFO] Running migrations..."
php artisan migrate --force

echo ""
echo "========================================"
echo "  Starting Development Server"
echo "========================================"
echo ""
echo "Server will be available at: http://localhost:8000"
echo "Press Ctrl+C to stop the server"
echo ""

php artisan serve

