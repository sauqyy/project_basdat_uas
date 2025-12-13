@echo off
echo ========================================
echo   Starting Laravel Application
echo ========================================
echo.

REM Check if .env exists
if not exist .env (
    echo [INFO] Creating .env file...
    if exist .env.example (
        copy .env.example .env
    ) else (
        echo [ERROR] .env.example not found. Please create .env manually.
        pause
        exit /b 1
    )
)

REM Check if APP_KEY is set
php artisan key:generate --show > nul 2>&1
if errorlevel 1 (
    echo [INFO] Generating application key...
    php artisan key:generate
)

REM Check if database.sqlite exists
if not exist database\database.sqlite (
    echo [INFO] Creating database file...
    type nul > database\database.sqlite
)

REM Run migrations
echo [INFO] Running migrations...
php artisan migrate --force

echo.
echo ========================================
echo   Starting Development Server
echo ========================================
echo.
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.

php artisan serve

pause

