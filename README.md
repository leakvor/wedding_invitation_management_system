# Wedding Invitation Management System

Monorepo starter for a full-stack wedding invitation management system.

## Structure

- `backend/`: Laravel API
- `frontend/`: Vue 3 + Vuetify SPA

## Core Features Included

- Public invitation page
- Guest lookup by name or code
- RSVP update
- Admin login
- Guest CRUD
- Money gift updates with history log
- Dashboard statistics
- Settings/content management
- QR code-ready guest payload field
- Excel export endpoint stub

## Suggested Setup

### Backend

1. Create a Laravel 11 project in `backend/` or copy these files into an existing Laravel app.
2. Install dependencies:
   - `laravel/sanctum`
   - `maatwebsite/excel` (optional for export)
3. Configure `.env` for MySQL or PostgreSQL.
4. Run:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend

1. In `frontend/`, install dependencies:

```bash
npm install
npm run dev
```

2. Set `VITE_API_BASE_URL` to your Laravel API base URL.

