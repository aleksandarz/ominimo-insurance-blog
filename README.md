# Ominimo Blog

A simple blog application built with Laravel, featuring authentication, full CRUD for posts, comments (including guest comments), and role-based authorization. Includes a React + TypeScript frontend integrated with a Laravel API.

Built as part of the Ominimo Insurance BE developer interview assignment.

## Features

- **Authentication** — registration, login, logout (Laravel Breeze)
- **Posts** — full CRUD, restricted to the post owner for edit/delete
- **Comments** — authenticated users and guests can comment; a comment can be deleted by its author, the post owner (moderation), or an admin
- **Authorization** — enforced via Laravel Policies (`PostPolicy`, `CommentPolicy`) and route middleware
- **Role-based access control** — an `admin` role can delete any post or comment
- **Two frontends**:
  - Server-rendered Blade views (`/posts`)
  - A React + TypeScript SPA consuming a JSON API (`/blog`), authenticated via Laravel Sanctum
- **Tests** — 53 PHPUnit tests (Unit + Feature) covering CRUD, validation, and authorization rules

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP |
| Database | MySQL |
| Auth | Laravel Breeze (Blade), Laravel Sanctum (API) |
| Frontend | React 18, TypeScript, React Router, Axios |
| Styling | Tailwind CSS |
| Testing | PHPUnit (SQLite in-memory) |

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL
- A local server environment (XAMPP, Laravel Herd, or similar)

## Setup

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd ominimo-blog
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JS dependencies**

   ```bash
   npm install
   ```

4. **Environment file**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Then set your database credentials in `.env`:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ominimo_blog
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Create the database (e.g. via phpMyAdmin or the MySQL CLI):

   ```sql
   CREATE DATABASE ominimo_blog;
   ```

5. **Run migrations and seed sample data**

   ```bash
   php artisan migrate --seed
   ```

   This creates an admin user, several regular users, and sample posts/comments (including guest comments).

   **Seeded login:**
   | Email | Password | Role |
   |---|---|---|
   | admin@example.com | AdminPass123! | admin |

   All other seeded users use the password `password`.

6. **Build frontend assets**

   ```bash
   npm run build
   ```

   (Or `npm run dev` for local development with hot-reload.)

7. **Serve the application**

   ```bash
   php artisan serve
   ```

   Visit:
   - `http://localhost:8000/posts` — Blade version
   - `http://localhost:8000/blog` — React version

## Running Tests

```bash
npm run build
php artisan test
```

> **Note:** `npm run build` must be run at least once before the test suite, since several Blade views reference compiled frontend assets via `@vite`.

## Project Structure Notes

- **Blade routes** live in `routes/web.php` and are session/cookie-authenticated.
- **API routes** live in `routes/api.php`, authenticated via Sanctum (cookie-based, since the React SPA is served from the same domain).
- **Authorization logic** is centralized in `app/Policies/PostPolicy.php` and `app/Policies/CommentPolicy.php` — both the Blade and API controllers call the same policies, so authorization rules are defined once.
- **React source** lives in `resources/js/`, entry point `app.tsx`, served from the `blog.blade.php` view.

## Deployment

_(Docker setup to be added.)_