# Setup Guide

## Prerequisites

Ensure the following are installed before proceeding.

| Requirement | Version |
|---|---|
| PHP | **8.0.2 or higher** (not 8.1+, see note below) |
| Composer | 2.x |
| MySQL | 5.7+ or 8.x |
| Node.js | 16.x+ (for asset compilation) |

> **PHP version note:** This project targets PHP `^8.0.2`. It was developed and tested on PHP 8.0. While it may run on PHP 8.1+, no guarantee is made — if you encounter deprecation errors or type errors on a newer PHP version, switch to PHP 8.0.

---

## 1. Clone and Install Dependencies

```bash
git clone <repository-url> future_app_backend
cd future_app_backend

composer install
npm install
```

---

## 2. Environment Configuration

Copy the example environment file and open it for editing:

```bash
cp .env.example .env
php artisan key:generate
```

### Database

Create a MySQL database named `future` (or any name you choose):

```sql
CREATE DATABASE future CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=future
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Mail (SendGrid)

The project uses SendGrid as its SMTP provider:

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

To get a SendGrid API key, create a free account at [sendgrid.com](https://sendgrid.com) and generate a key under **Settings > API Keys**.

### Pusher (Real-time Broadcasting)

Real-time messaging uses Pusher. Create a free app at [pusher.com](https://pusher.com):

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap2
```

### Firebase (Push Notifications)

Push notifications are sent via Firebase Cloud Messaging.

1. Go to the [Firebase Console](https://console.firebase.google.com) and open your project.
2. Navigate to **Project Settings > Service Accounts**.
3. Click **Generate new private key** and download the JSON file.
4. Place the file in the project root (e.g., `firebase-credentials.json`).
5. Set the path in `.env`:

```env
FIREBASE_CREDENTIALS=firebase-credentials.json
```

> Keep this file out of version control. It is already excluded via `.gitignore` if named `*.json` in the root — double-check before committing.

---

## 3. Database Migration

Run all migrations to create the schema:

```bash
php artisan migrate
```

To also seed initial data:

```bash
php artisan db:seed
```

---

## 4. Storage Link

Some features store uploaded files in `storage/app/public`. Create the symbolic link:

```bash
php artisan storage:link
```

---

## 5. Admin Panel

The Laravel Admin panel is available at `/admin`. On a fresh install, the default credentials are:

| Field | Value |
|---|---|
| Username | `admin` |
| Password | `admin` |

Change the password immediately after first login.

---

## 6. Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/api`.

To compile front-end assets (used by the admin panel):

```bash
npm run dev
```

---

## Common Issues

### `php artisan migrate` fails with "Access denied"
Double-check `DB_USERNAME` and `DB_PASSWORD` in `.env`. Ensure the database exists before running migrations.

### "Class not found" errors after cloning
Run `composer dump-autoload` to regenerate the autoloader.

### Firebase push notifications not sending
Confirm the `FIREBASE_CREDENTIALS` path points to a valid service account JSON file and that the file has not been corrupted.

### Email verification links not working
Ensure `APP_URL` in `.env` matches the URL the application is actually served from. Signed verification URLs will fail if the URL does not match.
