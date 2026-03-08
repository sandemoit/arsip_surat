# Repository Guidelines

## Project Structure & Module Organization
- Core Laravel app lives in `app/` (Livewire components under `app/Livewire`, HTTP controllers/middleware in `app/Http`).
- UI templates in `resources/views` (Volt pages in `resources/views/flux`, shared layouts/components in `resources/views/layouts` and `components`).
- Front-end assets: `resources/css`, `resources/js`; built output served from `public/` via Vite.
- Routing is defined in `routes/web.php`; auth handled by Fortify.
- Tests are split into `tests/Feature` and `tests/Unit`; seeds/migrations in `database/`.

## Setup, Build, and Development Commands
- `composer install` then `npm install` — install PHP and JS dependencies (copy `.env.example` to `.env` first).
- `php artisan key:generate` — set the app key; run after creating `.env`.
- `php artisan migrate --force` — apply database migrations (configure DB creds in `.env`).
- `npm run dev` — start Vite dev server (Hot Module Reloading for assets).
- `php artisan serve` — start HTTP server; pair with `php artisan queue:listen --tries=1` for background jobs.
- `composer dev` — convenience: runs serve + queue listener + Vite concurrently.
- `npm run build` — production asset build (used in deploys).

## Coding Style & Naming Conventions
- Follow PSR-12; PHP formatting enforced with `./vendor/bin/pint` (run before commits).
- Classes use `StudlyCase`, methods `camelCase`, config/env keys `SNAKE_CASE`.
- Livewire/Volt components should keep one feature per class/view; name routes/components with meaningful nouns (`ArsipUpload`, `DisposisiRiwayat`).
- Blade uses 4-space indentation; prefer Blade components/slots over inline HTML duplication.

## Testing Guidelines
- Primary suite: `php artisan test` (alias for PHPUnit 11). Use `--filter ClassName` for targeted runs.
- Place integration/UI flows in `tests/Feature`, pure logic in `tests/Unit`; mirror app namespaces.
- Use Laravel testing helpers (e.g., `RefreshDatabase`, HTTP assertions). Keep tests deterministic; avoid hitting external services.

## Commit & Pull Request Guidelines
- Prefer Conventional Commit-style summaries (`feat:`, `fix:`, `chore:`). Keep subject in imperative mood, <=72 chars.
- For PRs: include what/why, linked issue, setup/migration notes, and screenshots/GIFs for UI changes. Ensure `php artisan test` and `npm run build` (or `npm run dev` smoke) pass before requesting review.

## Security & Configuration Notes
- Store secrets only in `.env`; never commit it. `.env.example` lists required keys (DB, mail, storage, queue).
- After first install or when adding uploads, run `php artisan storage:link` so public assets resolve.
- Queue-dependent features require a running worker (`php artisan queue:listen`) in non-local environments.
