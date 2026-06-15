# Anesthesia Audit System

A web-based system developed to digitize and automate the anesthesia audit form used by the Department of Anaesthesia, Teaching Hospital Jaffna.

The system replaces manual paper-based records with a structured digital platform for recording patient details, surgical information, anesthesia methods, monitoring data, adverse events, and post-operative admissions.

## Purpose

- Improve data accuracy
- Simplify record keeping
- Enable easy search and retrieval
- Support clinical audit and quality improvement

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Vite
- Tailwind CSS

## Project Setup Guide

Follow these steps in order. Each step matters because the app depends on both the Laravel backend and the Vite frontend build.

### Step 1: Install prerequisites

Make sure these tools are available on your machine:

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm
- MySQL or MariaDB
- Git

You can verify them with:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

### Step 2: Get the project code

Clone the repository and open the project folder.

```bash
git clone <repository-url>
cd anesthesia-audit-system-fix-default-account
```

If you already have the folder locally, just open it in your editor and continue.

### Step 3: Install PHP dependencies

Install the Laravel dependencies with Composer.

```bash
composer install
```

This downloads the backend packages used by the application.

### Step 4: Create the environment file

If `.env` does not already exist, create it from `.env.example`.

```bash
copy .env.example .env
```

If you are using PowerShell and the file already exists, you can skip this step.

### Step 5: Configure the `.env` file

Open `.env` and set your local database settings.

Typical values are:

```env
APP_NAME="Anesthesia Audit System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

If you use SQLite instead, update the database settings accordingly.

### Step 6: Generate the application key

Laravel needs an application key for encryption and sessions.

```bash
php artisan key:generate
```

### Step 7: Create the database

Create an empty database in MySQL or MariaDB before migrating.

Example:

```sql
CREATE DATABASE anesthesia_audit_system;
```

Then update `DB_DATABASE` in `.env` to match that name.

### Step 8: Run the database migrations

This creates the tables used by the app.

```bash
php artisan migrate
```

If you want to start from a clean database, use:

```bash
php artisan migrate:fresh
```

### Step 9: Install frontend dependencies

Install the Vite and Tailwind packages.

```bash
npm install
```

### Step 10: Build the frontend assets

This step is required so Laravel can find `public/build/manifest.json`.

```bash
npm run build
```

If you skip this step, the app can throw `Illuminate\Foundation\ViteManifestNotFoundException` when a Blade view calls `@vite(...)`.

### Step 11: Start the application

Run the Laravel server:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

### Step 12: Use the development mode workflow

For active development, run the combined dev script instead of starting everything manually.

```bash
composer run dev
```

That script starts:

- Laravel server
- Queue listener
- Laravel Pail logs
- Vite dev server

### Step 13: Run tests

You can run the test suite with:

```bash
composer test
```

or directly:

```bash
php artisan test
```

## Common Setup Commands

These are the most useful commands for this project:

```bash
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
composer run dev
composer test
```

## Fixing the Vite Manifest Error

If you see `Vite manifest not found at public/build/manifest.json`, it means the frontend assets were not built yet.

Run:

```bash
npm install
npm run build
```

For local development, you can also keep the Vite dev server running with:

```bash
npm run dev
```

## Notes

- The homepage uses `@vite(...)`, so the frontend build is part of the setup.
- If you change files in `resources/css` or `resources/js`, rebuild the frontend assets.
- If the database is changed, rerun the migrations after updating `.env`.
