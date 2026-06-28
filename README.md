# Future+ Backend API

REST API backend for the **Future+** mobile application — a mental health and community support platform. Built with Laravel 9 on PHP 8.0.

---

## Prerequisites & Requirements

Ensure you have the following installed on your system before proceeding:

| Requirement | Version |
|---|---|
| **PHP** | `8.0.2` or higher (recommended PHP 8.0, see note below) |
| **Composer** | `2.x` |
| **MySQL** | `5.7+` or `8.x` |
| **Node.js & npm** | `16.x+` (for compiling assets) |

> [!NOTE]
> **PHP version compatibility:** This project targets PHP `^8.0.2`. It was developed and tested on PHP 8.0. If you encounter deprecation errors or type errors on newer PHP versions (8.1+), please switch to PHP 8.0.

---

## Installation & Setup Guide

Follow these steps to set up the project locally:

### 1. Clone the Repository & Install Dependencies

```bash
git clone <repository-url> future-backend
cd future-backend

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration

Copy the example environment template file to create your active `.env` file:

```bash
cp .env.example .env
php artisan key:generate
```

#### Database Configuration
1. Create a MySQL database (e.g. named `future`):
   ```sql
   CREATE DATABASE future CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Open `.env` and configure your database connection details:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=future
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

#### Mail (SendGrid)
Configure your mail driver using SendGrid API keys:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Pusher (Real-time Broadcasting)
Configure Pusher credentials for real-time messaging:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap2
```

#### Firebase (Push Notifications)
1. Download your service account credentials JSON file from the Firebase Console (**Project Settings > Service Accounts**).
2. Place the file in your project root (e.g., `firebase-credentials.json`).
3. Point to this file in your `.env`:
   ```env
   FIREBASE_CREDENTIALS=firebase-credentials.json
   ```
   *(Note: The `*.json` files in the root directory are already ignored in `.gitignore` to prevent committing credentials).*

---

### 3. Database Migration & Seeding

Create the database tables and seed the database with initial/default data:

```bash
php artisan migrate --seed
```

---

### 4. Create Storage Link

Create the symbolic link from `public/storage` to `storage/app/public` so uploads are publicly accessible:

```bash
php artisan storage:link
```

---

### 5. Compile Front-End Assets

Compile JavaScript and styling assets used by the admin panel:

```bash
# For development (live reloading/watching)
npm run dev

# Or compile for production
npm run build
```

---

### 6. Run the Application

Start the local PHP development server:

```bash
php artisan serve
```

The REST API will be available at `http://127.0.0.1:8000/api`.

---

## Admin Panel

An admin panel is available at `/admin` (e.g., `http://127.0.0.1:8000/admin`).

**Default Admin Credentials:**
- **Username:** `admin`
- **Password:** `admin`

> [!WARNING]
> Change the default admin password immediately after your first login under admin settings.

---

## Running Tests

Run the phpunit test suite to verify everything works:

```bash
php artisan test
```

For more testing configurations, check [TESTING.md](docs/TESTING.md).

---

## Tech Stack Summary

- **Backend Framework:** Laravel 9 (PHP 8.0.2+)
- **Database:** MySQL
- **Authentication:** Laravel Sanctum (Token-based authentication)
- **Real-Time Engine:** Pusher
- **Push Notifications:** Firebase Cloud Messaging (FCM)
- **Mail Carrier:** SendGrid (SMTP)
- **Admin System:** Laravel Admin
