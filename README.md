# HMNR DevOS

## Overview
HMNR DevOS is a modern developer workspace with an IDE-inspired dark interface. It acts as a highly integrated taxonomy of projects, problems, solutions, snippets, and markdown knowledge. Built on the core principle: **"Don't solve the same problem twice."**

## Features
- **Project Taxonomy:** Track projects, environments, and statuses.
- **Problem & Solution Tracking:** A structured case-study format for debugging logs.
- **Developer Vault:** Save reusable code snippets and markdown knowledge documents.
- **Global Search:** Find previously solved problems across all domains instantly (`Ctrl/Cmd + K`).
- **API Lab:** Test HTTP requests directly inside the platform with built-in SSRF protection and one-click documentation capabilities.
- **MVEL Playground:** Evaluate MVEL expressions against JSON variables securely via an isolated microservice.
- **Developer Activity Feed:** Dashboard visibility into recent changes, solved problems, and created snippets.

## Architecture
HMNR DevOS uses a monolithic core with server-side rendering, progressively enhanced via Alpine.js for minimal client state. See `docs/architecture.md` for complete details.

## Tech Stack
- **Backend:** PHP 8.3, Laravel 11/12
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** PostgreSQL
- **Testing:** PHPUnit

---

## Installation

### Local Development
1. Clone the repository and install PHP dependencies:
   ```bash
   composer install
   ```
2. Install Node dependencies and build assets:
   ```bash
   npm install && npm run build
   ```
3. Copy environment configuration and generate the app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

### Docker Development
For a containerized setup, a `docker-compose.yml` and `Dockerfile` are provided.
1. Run containers in the background:
   ```bash
   docker-compose up -d
   ```
2. Execute migrations inside the container:
   ```bash
   docker-compose exec app php artisan migrate
   ```

---

## Database Setup
Ensure PostgreSQL is running. Set the credentials in your `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hmnr_devos
DB_USERNAME=hmnr_devos
DB_PASSWORD=secret
```
Run migrations: `php artisan migrate`

---

## Testing
Run the test suite using PHPUnit. Comprehensive coverage includes CRUD operations, authorization, and SSRF security validations.
```bash
php artisan test
```

---

## Environment Variables
Review `.env.example` for all required variables. Do not commit actual secrets to source control.
Key variables:
- `APP_KEY`
- `DB_*` (Database config)
- `MVEL_EXECUTOR_URL` (Microservice endpoint)

---

## Security
Authorization (IDOR prevention) is strictly enforced via Laravel Policies on every entity.
See `docs/security.md` for a complete security overview.

---

## MVEL Executor
For security reasons, MVEL expressions are **never** executed within the Laravel PHP process. The MVEL Lab depends on an external Java microservice. Configure the endpoint via `MVEL_EXECUTOR_URL` in `.env`.

---

## API Lab
The API Lab features a robust proxy with rigid SSRF protection. It resolves DNS manually to prevent DNS rebinding attacks and explicitly blocks connections to internal/private IP ranges.

---

## Roadmap
- Vector embeddings / RAG for search improvements
- Team collaboration tools
- Webhook endpoints
