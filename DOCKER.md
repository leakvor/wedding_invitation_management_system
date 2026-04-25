## Docker Deployment

This project now includes a Docker Compose setup for:

- `frontend`: Vue app served by Nginx on `http://localhost:5173`
- `backend-nginx`: Laravel public app on `http://localhost:8000`
- `backend-app`: PHP-FPM container for Laravel
- `db`: MySQL 8 on host port `3307`

### 1. Prerequisites

Install:

- Docker Desktop
- Docker Compose

### 2. Start the stack

From the project root:

```powershell
cd "D:\leak project\money-money"
docker compose up --build -d
```

### 3. Generate app data

Run Laravel seeding once:

```powershell
docker compose exec backend-app php artisan db:seed
```

If you want a clean database:

```powershell
docker compose exec backend-app php artisan migrate:fresh --seed
```

### 4. Open the app

- Frontend: `http://localhost:5173`
- Backend API: `http://localhost:8000/api`

### 5. Default database credentials

Used by Compose:

- database: `wedding_invitation`
- username: `wedding`
- password: `wedding`
- root password: `root`

MySQL is exposed to your machine on:

- host: `127.0.0.1`
- port: `3307`

### 6. Stop the stack

```powershell
docker compose down
```

To remove database data too:

```powershell
docker compose down -v
```

### 7. Production notes

Before real deployment, update these values in `docker-compose.yml`:

- `APP_URL`
- `FRONTEND_URL`
- `VITE_API_BASE_URL`
- database passwords

If you deploy to a real domain, use values like:

- `APP_URL=https://api.yourdomain.com`
- `FRONTEND_URL=https://yourdomain.com`
- `VITE_API_BASE_URL=https://api.yourdomain.com/api`

### 8. Useful commands

View logs:

```powershell
docker compose logs -f
```

Open a shell in Laravel container:

```powershell
docker compose exec backend-app sh
```

Rebuild after code or Docker changes:

```powershell
docker compose up --build -d
```
