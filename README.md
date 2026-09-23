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

Phase 2 has started with stancl/tenancy 3.x and a database-per-tenant foundation. The central database now owns tenant and domain metadata, while tenant databases are provisioned and migrated through the package lifecycle pipeline. Business-domain migrations are now mirrored into database/migrations/tenant in dependency order. The legacy central migrations remain temporarily so the existing application continues to boot during the staged cutover. The provisioning test verifies that a tenant receives isolated users, products, orders, and remesas tables. Tenant routes now resolve by domain, tenancy bootstrap/revert listeners are registered explicitly, tenant databases are deleted with the tenant lifecycle, and an isolation test verifies that tenant A cannot read tenant B products.

For local development, configure TENANCY_CENTRAL_DOMAINS and use tenant domains such as shop.localhost. Production will use real subdomains/custom domains after the tenant schema migration is complete.

> Dependency note: stancl/tenancy 3.10.x is being used while the project remains on PHP 8.3. The Composer platform is pinned to PHP 8.3 so the lock file cannot resolve PHP 8.4-only Symfony releases. The committed lock file includes the tenancy dependency graph and is generated against the project platform.

## Identity and tenant access

Phase 3 now establishes the platform identity boundary. PlatformUser authenticates against the central users table through an explicit central connection, preventing authentication from switching to a tenant database.

tenant_memberships links a platform identity to one or more tenants with an explicit role and status.

Supported roles:

- owner
- admin
- manager
- staff
- customer

Protected tenant routes use auth plus EnsureTenantMembership. The middleware checks the active membership against the tenant already resolved by the tenancy context. A tenant ID supplied by a request is never accepted as an authorization claim.

The legacy tenant-local User model remains during the staged migration because existing commerce relationships still reference it. Operational user relationships will be migrated incrementally after tenant switching and policy coverage are in place.

See [Identity and access](docs/IDENTITY.md) for the transition and security rules. Tenant switching now exposes only active memberships and resolves the destination domain from central tenant metadata; see [Tenant switching](docs/TENANT-SWITCHING.md). Authorization policies now define tenant-scoped catalog and order permissions; see [Authorization](docs/AUTHORIZATION.md). The staged customer identity bridge now provisions tenant-local customer records from the central platform identity; see [Customer identity](docs/CUSTOMER-IDENTITY.md).

## Product direction

The target platform combines:

**Commerce · CRM · Orders · Payments · Marketing · Analytics**

The architecture is being migrated to **database-per-tenant** multi-tenancy with stancl/tenancy. Each business will have isolated operational data while the platform database manages tenants, domains and SaaS-level concerns.

See:

- [Roadmap](docs/ROADMAP.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Tenant schema strategy](docs/TENANT-SCHEMA.md)
- [Identity and access](docs/IDENTITY.md)

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

Configure the database in .env, then run:

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

CI is defined in .github/workflows/ci.yml and must be verified before advancing implementation phases. CI status is verified directly from the repository's GitHub Actions runs. The workflow validates Composer metadata, installs the committed lock file, prepares the Laravel application against MySQL 8.4, and runs the test suite.

## Docker

The project includes a FrankenPHP-based Dockerfile. Production container configuration will be hardened and documented as part of Phase 12.

## Development workflow

1. Inspect the current repository and CI state.
2. Implement one coherent phase/change.
3. Run automated tests and static/format checks applicable to the change.
4. Verify GitHub Actions.
5. Update this README as part of the merge.
6. Continue to the next phase only after the previous one is stable.

## License

MIT.
