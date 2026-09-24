# CombosPlus

**Commerce & Business Platform — Laravel 12 + Filament 5**

CombosPlus is evolving into a premium multi-tenant commerce platform for businesses that need a modern storefront and an operational back office.

## Current capabilities

- Product catalog and categories
- Offers
- Cart and checkout
- Orders and order history
- Addresses and profiles
- Payment methods and Zelle payment flow
- Remittances with platform identity tracking
- Tenant membership and role-based access
- Telegram notifications
- Filament administration with a premium KPI dashboard and operational table filters
- Laravel Octane
- Docker/FrankenPHP deployment foundation

## Multi-tenancy foundation

Phase 2 established the database-per-tenant foundation with stancl/tenancy 3.x. The central database owns tenant and domain metadata while tenant databases own operational commerce data. Tenant routes resolve by domain and isolation tests verify tenant separation.

## Identity and tenant access

Phase 3 establishes the platform identity boundary. PlatformUser authenticates against the central users table. tenant_memberships links a platform identity to one or more tenants with explicit role and status.

Supported roles: owner, admin, manager, staff, customer.

Protected tenant routes use auth plus EnsureTenantMembership. The middleware checks the active membership against the tenant already resolved by tenancy. A tenant ID supplied by a request is never accepted as an authorization claim.

Operational ownership for carts, addresses, orders and remittances now uses platform_user_id exclusively. Legacy tenant-local user_id fields and the legacy orders.payment_method field have been removed from the tenant schema after dependent reads/writes were eliminated. Profile email validation is anchored to the central PlatformUser connection. Checkout scopes addresses and payment methods to the current tenant identity, and order creation locks the customer cart to prevent concurrent checkout races.

Remittance payment submission is explicitly treated as a payment request and delegated to a dedicated application service. It transitions to procesando and does not mark the remittance pagado until a trusted provider transaction can be verified. Provider verification remains a separate integration boundary.

## Premium administration

Phase 4 now includes a compact dashboard KPI foundation plus operational filters for orders, products and customers. The administration tables prioritize searchable business identifiers, relevant status filters and a dense column hierarchy that keeps secondary metadata available without making the interface visually heavy.

See [Identity and access](docs/IDENTITY.md), [Customer identity](docs/CUSTOMER-IDENTITY.md), [Tenant switching](docs/TENANT-SWITCHING.md), [Authorization](docs/AUTHORIZATION.md), [Tenant routes](docs/TENANT-ROUTES.md), and [Tenant controller audit](docs/TENANT-CONTROLLER-AUDIT.md).

## Product direction

**Commerce · CRM · Orders · Payments · Marketing · Analytics**

See [Roadmap](docs/ROADMAP.md) and [Architecture](docs/ARCHITECTURE.md).

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

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

For development:

```bash
composer run dev
```

## Tests

```bash
composer test
```

CI is defined in .github/workflows/ci.yml and must be verified before advancing implementation phases. GitHub Actions is the source of truth for repository CI.

## Development workflow

1. Inspect repository and CI state.
2. Implement one coherent change.
3. Add or update automated tests.
4. Verify GitHub Actions.
5. Update this README as part of the merge.
6. Continue only after the previous change is stable.
