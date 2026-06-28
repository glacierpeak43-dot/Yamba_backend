# Future+ Backend API

REST API backend for the **Future+** mobile application — a mental health and community support platform. Built with Laravel 9 on PHP 8.0.

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 9 |
| PHP | 8.0.2+ |
| Database | MySQL |
| Authentication | Laravel Sanctum (token-based) |
| Real-time | Pusher |
| Push Notifications | Firebase Cloud Messaging (FCM) |
| Email | SendGrid (SMTP) |
| Admin Panel | Laravel Admin (`/admin`) |

## Core Features

- **Authentication** — register, login, email verification, password reset, referral links
- **Articles** — categorised articles with comments, replies, and voting
- **Forums** — community discussion boards with consent flow, comments, replies, and voting
- **Chats** — peer-to-peer messaging with friend requests, blocking, and reporting
- **Appointments** — booking system between users and counsellors
- **Emergency Contacts** — national helplines, police, ambulance, and fire station contacts
- **Alerts** — peer alert system with location support
- **Notifications** — in-app and push notifications via FCM
- **Levels & Awards** — gamification layer for user engagement
- **Admin Panel** — full CMS at `/admin` for content and user management

## Quick Start

See [SETUP.md](docs/SETUP.md) for a full installation walkthrough.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## Running Tests

See [TESTING.md](docs/TESTING.md) for details.

```bash
php artisan test
```

## API Base URL

All API routes are prefixed with `/api`. Authentication uses Bearer tokens issued by Sanctum.

Most protected routes require both:
- `Authorization: Bearer {token}` header
- A verified email address (`verified` middleware)
