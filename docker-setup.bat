@echo off
echo 🚀 Setting up Birthday Notification System with Docker...
echo.

REM Build and start containers
echo 📦 Building Docker containers...
docker compose up -d --build

REM Wait for containers to be ready
echo ⏳ Waiting for containers to start...
timeout /t 5 /nobreak >nul

REM Install Composer dependencies
echo 📥 Installing Composer dependencies...
docker compose exec app composer install --no-interaction

REM Create .env file if it doesn't exist
if not exist .env (
    echo 📝 Creating .env file...
    docker compose exec app cp .env.example .env
)

REM Generate application key
echo 🔑 Generating application key...
docker compose exec app php artisan key:generate

REM Create SQLite database
echo 💾 Creating SQLite database...
docker compose exec app touch database/database.sqlite

REM Run migrations
echo 🗃️ Running database migrations...
docker compose exec app php artisan migrate --force

REM Set permissions
echo 🔒 Setting permissions...
docker compose exec app chown -R www-data:www-data /var/www/storage
docker compose exec app chown -R www-data:www-data /var/www/bootstrap/cache

REM Build assets
echo 🎨 Building frontend assets...
docker compose run --rm node npm install
docker compose run --rm node npm run build

echo.
echo ✅ Setup complete!
echo.
echo 📋 Next steps:
echo 1. Create an admin user:
echo    docker compose exec app php artisan make:filament-user
echo.
echo 2. Access the application:
echo    http://localhost:8000
echo.
echo 3. Run birthday notifications:
echo    docker compose exec app php artisan birthdays:notify
echo.
echo 📌 Useful commands:
echo    docker compose up -d          # Start containers
echo    docker compose down           # Stop containers
echo    docker compose logs -f app    # View logs
echo    docker compose exec app bash  # Access container shell
echo.
pause
