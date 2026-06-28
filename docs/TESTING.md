# Testing Guide

## Overview

The project uses **PHPUnit 9** via `php artisan test`. Tests are split into two suites:

| Suite | Directory | Purpose |
|---|---|---|
| Unit | `tests/Unit/` | Isolated logic tests (no database, no HTTP) |
| Feature | `tests/Feature/` | HTTP-level tests against the full application stack |

---

## Running Tests

### Run all tests

```bash
php artisan test
```

### Run a specific suite

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Run a specific test file

```bash
php artisan test tests/Feature/ExampleTest.php
```

### Run a specific test method

```bash
php artisan test --filter test_the_application_returns_a_successful_response
```

---

## Test Environment

Tests use a separate environment defined in `phpunit.xml`. Key overrides:

| Setting | Test Value | Why |
|---|---|---|
| `APP_ENV` | `testing` | Disables production guards |
| `CACHE_DRIVER` | `array` | In-memory, no persistence |
| `MAIL_MAILER` | `array` | Captures emails, nothing is sent |
| `QUEUE_CONNECTION` | `sync` | Jobs run immediately inline |
| `SESSION_DRIVER` | `array` | No file I/O |
| `BCRYPT_ROUNDS` | `4` | Faster password hashing in tests |

### Database

The test suite runs against your configured MySQL database by default (the `DB_CONNECTION` in `.env`). To avoid polluting your development data, you can either:

**Option A — Use a separate test database (recommended):**

Create a `future_test` database and add this to `.env` or export it before running tests:

```bash
DB_DATABASE=future_test php artisan test
```

**Option B — Use SQLite in-memory:**

Uncomment these two lines in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

> Note: Some migrations use MySQL-specific syntax. Test thoroughly after switching drivers.

Use `RefreshDatabase` in test classes that need a clean slate:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}
```

---

## Writing Tests

### Feature test skeleton

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_articles(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/article_categories');

        $response->assertOk();
    }
}
```

### Authenticated requests

Most API routes require a Sanctum token and a verified email. Use `actingAs` with the `sanctum` guard:

```php
$user = User::factory()->create(['email_verified_at' => now()]);
$this->actingAs($user, 'sanctum');
```

### Unit test skeleton

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleUnitTest extends TestCase
{
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
}
```

Unit tests extend `PHPUnit\Framework\TestCase` directly (not Laravel's `TestCase`) so no application bootstrap occurs — keep them fast and dependency-free.

---

## Factories

A `UserFactory` is available at `database/factories/UserFactory.php`. Use it to generate test users:

```php
$user = User::factory()->create();                          // persisted
$user = User::factory()->make();                           // in-memory only
$users = User::factory()->count(5)->create();
```

---

## Viewing Test Output

For more verbose output:

```bash
php artisan test --verbose
```

For a summary with timing:

```bash
php artisan test --profile
```
