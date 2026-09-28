# Deployment Guide

HMNR DevOS is container-ready. 

## Docker Compose
A `docker-compose.yml` is provided in the root directory for rapid deployment containing:
- `app`: PHP 8.3-FPM backend with all required extensions
- `web`: Nginx reverse proxy
- `postgres`: PostgreSQL 15 Database

### Quickstart
```bash
# 1. Clone repository
git clone https://github.com/.../hmnr-devos.git
cd hmnr-devos

# 2. Setup Environment
cp .env.example .env
# Edit .env database credentials

# 3. Spin up containers
docker-compose up -d

# 4. Migrate and Seed
docker-compose exec app php artisan migrate
```

## Considerations
- The `mvel-executor` service in the Docker compose file is a placeholder. You must provide a valid Java HTTP endpoint capable of interpreting MVEL. 
- Ensure `APP_KEY` is generated in production.
- For production, swap `SESSION_DRIVER` to `redis` or `database` rather than `file`.

