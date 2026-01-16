@echo off
echo ========================================
echo Birthday Notification System - Docker Setup
echo ========================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not running!
    echo Please start Docker Desktop and try again.
    pause
    exit /b 1
)

echo Step 1: Building Docker containers...
docker compose up -d --build
if errorlevel 1 (
    echo ERROR: Failed to build containers!
    pause
    exit /b 1
)

echo.
echo Step 2: Waiting for containers to start...
timeout /t 10 /nobreak >nul

echo.
echo Step 3: Installing Composer dependencies...
docker compose exec -T app composer install --no-interaction --optimize-autoloader
if errorlevel 1 (
    echo WARNING: Composer install failed, trying again...
    timeout /t 5 /nobreak >nul
    docker compose exec -T app composer install --no-interaction
)

echo.
echo Step 4: Setting up environment file...
if not exist .env (
    echo Creating .env file...
    copy .env.example .env >nul
)

echo.
echo Step 5: Generating application key...
docker compose exec -T app php artisan key:generate --force

echo.
echo Step 6: Creating database...
docker compose exec -T app mkdir -p database
docker compose exec -T app sh -c "touch database/database.sqlite"

echo.
echo Step 7: Running migrations...
docker compose exec -T app php artisan migrate --force

echo.
echo Step 8: Setting permissions...
docker compose exec -T app chmod -R 775 storage bootstrap/cache

echo.
echo Step 9: Installing NPM dependencies...
docker compose run --rm node npm install

echo.
echo Step 10: Building assets...
docker compose run --rm node npm run build

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next step: Create an admin user
echo Run this command:
echo   docker compose exec app php artisan make:filament-user
echo.
echo Then open your browser to:
echo   http://localhost:8000
echo.
pause
