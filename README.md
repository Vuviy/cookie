# Cookie Authentication — Remember Me

A secure "Remember Me" authentication system built with **pure PHP** (no frameworks).
Implements the selector/validator pattern to protect persistent login cookies
against theft and timing attacks.

## How It Works

When a user logs in with "Remember Me" checked, two random tokens are generated:
a **selector** (used to look up the record in the database) and a **validator**
(used to verify authenticity). Only the SHA-256 hash of the validator is stored
in the database — so even if the database is compromised, the raw token cannot
be reused. On every auto-login the token pair is rotated, invalidating the old cookie.

## Features

- **Selector/validator pattern** — lookup and verification are separated to prevent timing attacks
- **Token rotation** — new selector and validator generated on every auto-login (30-day sliding window)
- **Device fingerprinting** — cookie is bound to User-Agent hash to detect token theft
- **Logout and logout-all** — single device or all active sessions can be invalidated
- **Secure cookie flags** — `HttpOnly`, `Secure`, `SameSite=Lax`
---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3 (no framework) |
| Database | MySQL 8.4 |
| Hashing | SHA-256 (via PHP `hash()`) |
| Web server | Nginx 1.27 |
| Infrastructure | Docker |

---

## Project Structure

Everything lives in a single repository:

```
cookie/
  ├── app/               ← PHP application
  ├── docker/            ← Nginx config, PHP Dockerfile
  ├── docker-compose.yml
  └── .env
```

---

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/) and Docker Compose installed
- Git

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/Vuviy/cookie.git
cd cookie
```

**2. Copy the environment file**

```bash
cp .env.example .env
cp app/.env.example app/.env
```

**3. Configure `.env`**

```env
DB_DATABASE=db_cookie
MYSQL_ROOT_PASSWORD=root
```

```env
DB_HOST=db_cookie
DB_NAME=db_cookie
USER_NAME=root
PASSWORD=root
```

**4. Start Docker containers**

```bash
docker compose up -d
```

**5. Install dependencies**

```bash
docker exec cookie_php composer install
```

**6. Create the sessions table**

Run the SQL in phpMyAdmin (`http://localhost:8000`):

```sql
CREATE TABLE remember_tokens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

 user_id BIGINT UNSIGNED NOT NULL,

 selector BINARY(16) NOT NULL,
 validator_hash BINARY(32) NOT NULL,

 fingerprint BINARY(32) NOT NULL,

 expires_at DATETIME NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 last_used_at DATETIME NULL,

 CONSTRAINT uniq_selector UNIQUE (selector),
 CONSTRAINT fk_remember_tokens_user
     FOREIGN KEY (user_id) REFERENCES users(id)
         ON DELETE CASCADE,

 INDEX idx_user_id (user_id),
 INDEX idx_expires_at (expires_at)
);
```

**7. Open in browser**

```
http://localhost
```

---

## Database GUI

phpMyAdmin is available at:

```
http://localhost:8000
```

Login with credentials from your `.env` file (`MYSQL_ROOT_PASSWORD`).

---

## Usage Example

```php
$repo           = new CookieRepository(new Database(config()));
$rememberMe     = new RememberMeService($repo);

// On login (if "Remember Me" checkbox is checked)
$rememberMe->createToken($userId);

// On every protected page — auto-login if cookie exists
$rememberMe->tryAutoLogin();

// Logout from current device
$rememberMe->logout();

// Logout from all devices
$rememberMe->logoutAll($userId);
```
---

## License

This project is open-source and available under the [MIT license](LICENSE).
