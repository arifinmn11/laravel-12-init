#!/bin/bash

# Laravel MCP Boilerplate - Setup Script
# Run this after upgrading to PHP 8.2+

echo "🚀 Laravel MCP Boilerplate Setup"
echo "================================"
echo ""

# Step 1: Check PHP version
echo "Step 1: Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP version: $PHP_VERSION"
echo ""

# Step 2: Create MySQL database (requires manual input)
echo "Step 2: Creating MySQL database..."
echo "Please enter your MySQL root password when prompted:"
mysql -u root -p <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS laravel_mcp_boilerplate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES LIKE 'laravel_mcp_boilerplate';
EXIT;
MYSQL_SCRIPT

if [ $? -eq 0 ]; then
    echo "✓ Database created successfully!"
else
    echo "⚠ Database creation failed. Please create it manually."
    echo "Run: CREATE DATABASE laravel_mcp_boilerplate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    exit 1
fi
echo ""

# Step 3: Generate app key if not exists
echo "Step 3: Generating application key..."
if grep -q "APP_KEY=$" .env; then
    php artisan key:generate
    echo "✓ Application key generated"
else
    echo "✓ Application key already exists"
fi
echo ""

# Step 4: Run migrations
echo "Step 4: Running database migrations..."
php artisan migrate
echo "✓ Migrations completed"
echo ""

# Step 5: Install Passport
echo "Step 5: Installing Laravel Passport..."
php artisan passport:install
echo "✓ Passport installed - SAVE THE CLIENT SECRET!"
echo ""

# Step 6: Publish Spatie permissions
echo "Step 6: Publishing Spatie Permission config..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
echo "✓ Permissions config published"
echo ""

# Step 7: Run permission migrations
echo "Step 7: Running permission migrations..."
php artisan migrate
echo "✓ Permission migrations completed"
echo ""

# Step 8: Seed roles and permissions
echo "Step 8: Seeding roles and permissions..."
php artisan db:seed --class=RoleSeeder
echo "✓ Roles seeded (admin, user)"
echo ""

# Step 9: Clear caches
echo "Step 9: Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
echo "✓ Caches cleared"
echo ""

echo "🎉 Setup Complete!"
echo "=================="
echo ""
echo "Next steps:"
echo "1. Start the server: php artisan serve"
echo "2. Start MCP server (optional): php artisan boost:serve"
echo "3. Visit API docs: http://127.0.0.1:8000/docs/api"
echo ""
echo "Create admin user:"
echo "php artisan tinker"
echo "\$user = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password')]);"
echo "\$user->assignRole('admin');"
echo "exit"
echo ""
