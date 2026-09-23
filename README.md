# CombosPlus

**Commerce & Business Platform — Laravel 12 + Filament 5**

CombosPlus is evolving into a premium multi-tenant commerce platform for businesses that need a modern storefront and an operational back office.

## Current capabilities

The current application includes commerce and operational flows such as:

- Product catalog and categories
- Offers
- Cart and checkout
- Orders and order history
- Addresses and profiles
- Payment methods and Zelle payment flow
- Remittances
- Telegram notifications
- Filament administration
- Laravel Octane
- Docker/FrankenPHP deployment foundation

## Product direction

The target platform combines:

**Commerce · CRM · Orders · Payments · Marketing · Analytics**

The architecture is being migrated to **database-per-tenant** multi-tenancy with `stancl/tenancy`. Each business will have isolated operational data while the platform database manages tenants, domains and SaaS-level concerns.

See:

- [Roadmap](docs/ROADMAP.md)
- [Architecture](docs/ARCHITECTURE.md)

## Technology

- PHP 8.3+
- Laravel 12
- Filament 5
- Livewire
- Tailwind CSS 4
- Vite
- MySQL/PostgreSQL
- Redis
- Laravel Octane
- FrankenPHP
- stancl/tenancy
- PHPUnit
- Docker
- GitHub Actions

## Local development

Install PHP and Composer dependencies:

```bash
composer install
```

Create the environment:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`, then run:

```bash
php artisan migrate
npm install
npm run build
```

For development:

```bash
composer run dev
```

## Tests

Run the application test suite with:

```bash
composer test
```

CI is defined in `.github/workflows/ci.yml` and must be verified before advancing implementation phases.

## Docker

The project includes a FrankenPHP-based Dockerfile. Production container configuration will be hardened and documented as part of Phase 0/Phase 12.

## Development workflow

1. Inspect the current repository and CI state.
2. Implement one coherent phase/change.
3. Run automated tests and static/format checks applicable to the change.
4. Verify GitHub Actions.
5. Update this README as part of the merge.
6. Continue to the next phase only after the previous one is stable.

## License

MIT.
