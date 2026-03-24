# Custom Suit Platform (Laravel 11 + Vue workflow)

Updated to follow the **Laravel 11 pattern of using Vue directly inside the Laravel app** (via Vite entrypoints in `resources/js`).

## Structure
- `backend/`
  - `app/*` (controllers/services/support)
  - `database/seeders/InitialSeeder.php`
  - `resources/js/*` (Vue apps)
  - `resources/views/*.blade.php` (storefront/admin entry views)
  - `routes/api.php`, `routes/web.php`
  - `public/index.php`
  - `artisan` (minimal seed/reset commands)

## Run workflow

### 1) Seed initial data
```bash
php backend/artisan db:seed
```

### 2) Start backend (serves `/` and `/admin` + API)
```bash
php -S 127.0.0.1:8000 -t backend/public
```

### 3) Install frontend deps (inside backend)
```bash
cd backend
npm install
```

### 4) Start Vite (inside backend)
```bash
npm run dev
```

Now open:
- `http://127.0.0.1:8000/` (storefront)
- `http://127.0.0.1:8000/admin` (admin)

## Verify core functionality quickly
With backend running:
```bash
bash backend/scripts/smoke_test.sh
```

## API
- `GET /api/v1/health`
- `GET /api/v1/fabrics`
- `POST /api/v1/configurations/price-preview`
- `POST /api/v1/me/saved-configurations`
- `GET /api/v1/me/saved-configurations?email=...`
- `POST /api/v1/orders`
- `GET /api/v1/orders/{orderNumber}`
- `GET /api/v1/admin/fabrics` (admin key)
- `POST /api/v1/admin/fabrics` (admin key)
- `DELETE /api/v1/admin/fabrics/{id}` (admin key)
- `GET /api/v1/admin/orders` (admin key)

## Note
External package registry access may be blocked in this environment; structure and workflow are prepared for standard Laravel 11 + Vue setup.
