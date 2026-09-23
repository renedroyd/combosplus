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

## Multi-tenancy foundation

Phase 2 has started with `stancl/tenancy` 3.x and a database-per-tenant foundation. The central database now owns tenant and domain metadata, while tenant databases are provisioned and migrated through the package lifecycle pipeline. Business-domain migrations remain in the central migration tree temporarily. The tenant-schema migration plan is documented in `docs/TENANT-SCHEMA.md`; the next implementation step is to build the tenant schema in parallel, then perform the application cutover once provisioning and isolation tests are green.

For local development, configure `TENANCY_CENTRAL_DOMAINS` and use tenant domains such as `shop.localhost`. Production will use real subdomains/custom domains after the tenant schema migration is complete.

> Dependency note: `stancl/tenancy` 3.10.x is being used while the project remains on PHP 8.3. The Composer platform is pinned to PHP 8.3 so the lock file cannot resolve PHP 8.4-only Symfony releases. The committed lock file includes the tenancy dependency graph and is generated against the project platform.

## Product direction

The target platform combines:

**Commerce · CRM · Orders · Payments · Marketing · Analytics**

The architecture is being migrated to **database-per-tenant** multi-tenancy with `stancl/tenancy`. Each business will have isolated operational data while the platform database manages tenants, domains and SaaS-level concerns.

See:

- [Roadmap](docs/ROADMAP.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Tenant schema strategy](docs/TENANT-SCHEMA.md)

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

CI is defined in `.github/workflows/ci.yml` and must be verified before advancing implementation phases. CI status is now verified directly from the repository's GitHub Actions runs. The workflow validates Composer metadata, installs the committed lock file, prepares the Laravel application against MySQL 8.4, and runs the test suite.

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
