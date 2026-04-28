<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Installation

### Prerequisites

- PHP >= 8.3
- Composer
- SQLite (file-based, no server required)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/msofiullahs/binar-test.git
   cd binar-test
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**

   This application uses SQLite by default. No additional configuration is needed - the database file will be created automatically at `database/database.sqlite`.

   If you need to customize the database location, update the `.env` file:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/your/database.sqlite
   ```

   > **Note:** For SQLite, you only need to specify the `DB_CONNECTION` and optionally `DB_DATABASE`. Other database credentials (host, port, username, password) are not required.

5. **Run migrations and seeding data**
   ```bash
   php artisan migrate --seed
   ```

### Quick Setup

Alternatively, you can use the setup script:
```bash
composer setup
```

## Running the Application

### Development Server

To start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

### Full Development Environment

To run the server, queue worker, logs, and Vite dev server concurrently:
```bash
composer run dev
```

## API Documentation

This application provides a RESTful API for user management. All API endpoints are prefixed with `/api`.

### Authentication

The API uses Laravel Sanctum for token-based authentication.

---

### 1. Get Token

Obtain an authentication token by providing valid credentials.

**Endpoint:** `POST /api/token`

**Authentication:** Not required

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "your_password"
}
```

**Response (Success - 201):**
```json
{
    "token": "1|abc123...",
    "type": "Bearer"
}
```

**Response (Error - 401):**
```json
{
    "message": "The provided credentials are incorrect."
}
```

**Response (Validation Error - 422):**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

#### Example Login Credentials (from UserSeeder)

You can use the following credentials to obtain tokens for different roles:

| Role          | Email                  | Password  |
|---------------|------------------------|-----------|
| Administrator | admin@example.com      | password  |
| Manager       | manager1@example.com   | password  |
| User          | user1@example.com      | password  |

**Example: Login as Administrator**
```bash
curl -X POST http://localhost:8000/api/token \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

**Example: Login as Manager**
```bash
curl -X POST http://localhost:8000/api/token \
  -H "Content-Type: application/json" \
  -d '{"email":"manager1@example.com","password":"password"}'
```

**Example: Login as User**
```bash
curl -X POST http://localhost:8000/api/token \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@example.com","password":"password"}'
```

---

### 2. Get User List

Retrieve a paginated list of active users. This endpoint supports optional authentication.

**Endpoint:** `GET /api/users`

**Authentication:** Optional (uses `AuthenticateOptional` middleware)

**Query Parameters:**

| Parameter | Type   | Description                              |
|-----------|--------|------------------------------------------|
| search    | string | Filter users by name                     |
| sortBy    | string | Sort field (`created_at`, `name`, `id`)  |

**Examples:**

- Get all users (no authentication):
  ```bash
  curl http://localhost:8000/api/users
  ```

- Get users with search term:
  ```bash
  curl "http://localhost:8000/api/users?search=john"
  ```

- Get users sorted by name:
  ```bash
  curl "http://localhost:8000/api/users?sortBy=name"
  ```

- Get users with authentication token:
  ```bash
  curl -H "Authorization: Bearer YOUR_TOKEN" "http://localhost:8000/api/users"
  ```

**Response (Success - 200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user",
            "active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/users?page=1",
        "last": "http://localhost:8000/api/users?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost:8000/api/users",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

---

### 3. Create User

Create a new user account. Requires authentication and appropriate role.

**Endpoint:** `POST /api/user`

**Authentication:** Required (Bearer token via Sanctum)

**Authorization:** Requires authenticated user with appropriate role. The `EnsureUserRole` middleware applies the following rules:
- Managers cannot create users with `administrator` or `manager` roles

**Request Headers:**
```
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
    "email": "newuser@example.com",
    "password": "securepassword123",
    "name": "New User",
    "role": "user"
}
```

**Field Requirements:**

| Field    | Type   | Required | Description                                    |
|----------|--------|----------|------------------------------------------------|
| email    | string | Yes      | Must be a valid, unique email address          |
| password | string | Yes      | Minimum 8 characters                           |
| name     | string | Yes      | User's full name                               |
| role     | string | No       | Valid UserRoles enum (defaults to "user")      |

**Response (Success - 201):**
```json
{
    "id": 2,
    "name": "New User",
    "email": "newuser@example.com",
    "role": "user",
    "active": true,
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
}
```

**Response (Validation Error - 422):**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

**Response (Authorization Error - 422):**
```json
{
    "status": "error",
    "message": "Unable to create user with selected role"
}
```

**Response (Unauthenticated - 401):**
```json
{
    "message": "Unauthenticated."
}
```

---

### Available User Roles

The application uses the `UserRoles` enum with the following values:
- `administrator`
- `manager`
- `user`

### Available Sort Fields

The `UserSort` enum defines valid sort fields for the user list endpoint.

## Notifications

When a new user is created:
- The new user receives a `UserCreation` notification
- All administrators receive a `UserCreation` notification about the new user

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
