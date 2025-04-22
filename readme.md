# 🔐 Laravel Secure Audit Logging System

This project contains **two Laravel 12 applications** working together to securely log user-related events using JWT authentication and IP whitelisting.

- **Main App:** Handles user registration and emits audit logs.
- **Audit Service:** Receives and stores audit logs securely.

---

## 📁 Folder Structure

```
laravel-secure-audit-logger/
├── main-service/         # User system (emits logs)
└── audit-service/        # Audit system (receives logs)
```

---

## ⚙️ Requirements

- PHP 8.1+
- Composer
- Laravel 12
- `firebase/php-jwt` library
- SQLite/MySQL or any supported database

---

## 🚀 Setup Instructions

### 1. Clone the Repo

```bash
git clone https://github.com/snlt11/laravel-secure-audit-logger
cd laravel-secure-audit-logger
```

---

### 2. Main App Setup (`main-service/`)

```bash
cd main-service
composer install
cp .env.example .env
php artisan key:generate
```

Update the `.env` with:

```env
APP_NAME=MainApp
AUDIT_API_URL=http://127.0.0.1:8001/api/audit-logs
AUDIT_SECRET_KEY=your_super_secret_key
AUDIT_APP_NAME=MainApp
```

Run migrations:

```bash
php artisan migrate
php artisan serve --port=8000
```

---

### 3. Audit Service Setup (`audit-service/`)

```bash
cd ../audit-service
composer install
cp .env.example .env
php artisan key:generate
```

Update the `.env` with:

```env
APP_NAME=AuditService
AUDIT_SECRET_KEY=your_super_secret_key
AUDIT_APP_NAME=MainApp
MAIN_APP_IP=127.0.0.1
```

Run migrations:

```bash
php artisan migrate
php artisan serve --port=8001
```

---

## 🔐 Security Measures

- ✅ JWT signed with **HS256** and shared secret key.
- ✅ JWT includes **expiration (`exp`)** and **issued-at (`iat`)** time.
- ✅ Validates **`iss` claim** (issuer).
- ✅ Validates **source IP** (`MAIN_APP_IP`).
- ✅ Token expires after **1 minute**.

---

## 🔁 Upgraded: RS256 Key-Pair Support

A command is planned to generate **RSA key pairs**:

```bash
php artisan jwt:generate-keys
```

This will produce:
- `storage/app/keys/private.key`
- `storage/app/keys/public.key`

These can be used with **RS256** for enhanced security.

---

## ⚙️ JWT Payload Example

```json
{
  "type": "user_registered",
  "data": {
    "user_id": 1,
    "email": "test@example.com",
    "name": "Test User",
    "created_at": "2025-04-22T12:00:00Z"
  },
  "iat": 1713778800,
  "exp": 1713778860,
  "iss": "MainApp"
}
```

---

## 📁 Important Files

### `main-service/`

- `app/Listeners/SendAuditLog.php`: Sends JWT-signed log via HTTP
- `.env`: Contains secret key and audit URL

### `audit-service/`

- `app/Http/Controllers/AuditLogController.php`: Verifies and stores logs
- `.env`: Contains secret key, trusted IP, and issuer

---

## 🧪 Testing the Flow

1. Register a user in `main-service` (e.g. `/register`).
2. `SendAuditLog` listener sends the log to `audit-service`.
3. `audit-service` verifies JWT + IP and stores the log in DB.
4. Response: `{"message": "Audit log saved."}`

---

Built with ❤️ by Sai Nay Lin Thar

