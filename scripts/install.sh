#!/bin/bash

set -e

echo "=================================="
echo "  AllGames - Installation Script"
echo "=================================="
echo ""

# Copy .env.local if not exists
if [ ! -f ".env.local" ]; then
    echo "📄 Creating .env.local from .env.example..."
    cp .env.example .env.local
else
    echo "✅ .env.local already exists, skipping."
fi

# Ask for DATABASE_URL
echo ""
echo "🔧 Database configuration:"
echo "Current DATABASE_URL in .env.local:"
grep DATABASE_URL .env.local || echo "(not set)"
echo ""
read -p "Enter DATABASE_URL (press Enter to keep current): " DB_URL

if [ -n "$DB_URL" ]; then
    # Replace DATABASE_URL in .env.local
    if grep -q "DATABASE_URL=" .env.local; then
        sed -i "s|DATABASE_URL=.*|DATABASE_URL=\"$DB_URL\"|" .env.local
    else
        echo "DATABASE_URL=\"$DB_URL\"" >> .env.local
    fi
    echo "✅ DATABASE_URL updated."
fi

# Install dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install

# Create database
echo ""
echo "🗄️  Creating database..."
php bin/console doctrine:database:create --if-not-exists

# Run migrations
echo ""
echo "🔄 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
echo ""
echo "🌱 Loading fixtures..."
php bin/console doctrine:fixtures:load --no-interaction

# Build assets
echo ""
echo "🎨 Building Tailwind CSS..."
php bin/console tailwind:build 2>/dev/null || echo "⚠️  Tailwind build skipped (run manually: php bin/console tailwind:build --watch)"

echo ""
echo "=================================="
echo "✅ Installation complete!"
echo "=================================="
echo ""
echo "🌐 Starting Symfony server..."
echo ""
echo "Test accounts:"
echo "  Admin: username=admin  password=admin123  → /admin"
echo "  User:  username=user   password=user123"
echo ""
echo "Available routes:"
echo "  /             → Home"
echo "  /game/list    → All games"
echo "  /about        → About"
echo "  /login        → Login"
echo "  /wishlist     → Wishlist (login required)"
echo "  /admin        → Admin (ROLE_ADMIN required)"
echo ""

symfony server:start
