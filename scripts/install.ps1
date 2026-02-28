# AllGames - Windows Installation Script
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  AllGames - Installation Script" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Copy .env.local if not exists
if (-Not (Test-Path ".env.local")) {
    Write-Host "Creating .env.local from .env.example..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env.local"
} else {
    Write-Host ".env.local already exists, skipping." -ForegroundColor Green
}

# Ask for DATABASE_URL
Write-Host ""
Write-Host "Database configuration:" -ForegroundColor Cyan
$currentDB = Select-String -Path ".env.local" -Pattern "DATABASE_URL" | Select-Object -First 1
Write-Host "Current: $currentDB"
Write-Host ""
$dbUrl = Read-Host "Enter DATABASE_URL (press Enter to keep current)"

if ($dbUrl -ne "") {
    $content = Get-Content ".env.local"
    $content = $content -replace 'DATABASE_URL=.*', "DATABASE_URL=`"$dbUrl`""
    Set-Content ".env.local" $content
    Write-Host "DATABASE_URL updated." -ForegroundColor Green
}

# Install dependencies
Write-Host ""
Write-Host "Installing PHP dependencies..." -ForegroundColor Yellow
composer install

# Create database
Write-Host ""
Write-Host "Creating database..." -ForegroundColor Yellow
php bin/console doctrine:database:create --if-not-exists

# Run migrations
Write-Host ""
Write-Host "Running migrations..." -ForegroundColor Yellow
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
Write-Host ""
Write-Host "Loading fixtures..." -ForegroundColor Yellow
php bin/console doctrine:fixtures:load --no-interaction

# Build assets
Write-Host ""
Write-Host "Building Tailwind CSS..." -ForegroundColor Yellow
php bin/console tailwind:build 2>$null

Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host "Installation complete!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "Test accounts:" -ForegroundColor Cyan
Write-Host "  Admin: username=admin  password=admin123  -> /admin"
Write-Host "  User:  username=user   password=user123"
Write-Host ""
Write-Host "Starting Symfony server..." -ForegroundColor Yellow
Write-Host ""

symfony server:start
