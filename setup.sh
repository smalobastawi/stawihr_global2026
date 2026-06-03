#!/bin/bash

# Setup script for stawihr_global1 project
# This script automates the setup process for the project on a server.

# Exit immediately if any command fails
echo "Starting setup for stawihr_global1 project..."

# Step 1: Clone the repository
echo "Cloning the repository..."
git clone https://github.com/stawitech/stawihr_global1.git
cd stawihr_global1 || exit 1

# Step 2: Install PHP dependencies using Composer
echo "Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Step 3: Install frontend dependencies using npm
echo "Installing frontend dependencies..."
npm install

# Step 4: Create storage folders and set permissions
echo "Creating storage folders and setting permissions..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions for storage and bootstrap/cache directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Step 5: Copy .env.example to .env , .htaccess.example to public/.htaccess and generate application key
echo "Setting up environment..."
cp .env.example .env
cp public/.htaccess.example public/.htaccess
php artisan key:generate

# Step 6: Run database migrations and seeders
echo "Running database migrations and seeders..."
php artisan migrate --seed --force

# Step 7: Compile frontend assets
echo "Compiling frontend assets..."
npm run dev

# Step 8: Set up storage link
echo "Creating storage link..."
php artisan storage:link

echo "Setup completed successfully!"