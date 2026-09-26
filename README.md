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
- Filament administration with a premium KPI dashboard, operational filters, task-oriented navigation, compact table actions, refined layout density and standardized lightweight table UX and responsive dark-mode support
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

Phase 4 includes a compact dashboard KPI foundation, operational filters for orders, products and customers, task-oriented navigation groups, compact record actions with accessible tooltips and a refined admin shell. The latest layout pass reduces brand/header footprint, gives content a full-width workspace and uses a standardized sidebar width while preserving desktop collapsing.

The table design-system pass now centralizes lightweight defaults across the administration tables: contextual search placeholders, a short search debounce, consistent pagination options and mobile stacking. The responsive/dark-mode pass enables Filament dark mode with the system theme as the default, without adding custom CSS or JavaScript weight. Business-specific filters, actions and status semantics remain local to each resource.

See [Identity and access](docs/IDENTITY.md), [Customer identity](docs/CUSTOMER-IDENTITY.md), [Tenant switching](docs/TENANT-SWITCHING.md), [Authorization](docs/AUTHORIZATION.md), [Tenant routes](docs/TENANT-ROUTES.md), and [Tenant controller audit](docs/TENANT-CONTROLLER-AUDIT.md).

## Marketplace direction

CombosPlus is now evolving from a tenant storefront/back-office foundation into a **multi-tenant Marketplace**. The public Marketplace is the primary discovery surface; tenant administration remains the operational back office.

### Marketplace roadmap

1. **Marketplace Core** — public Marketplace home, store discovery, public store pages, public product pages, lightweight search, and a simple store-registration entry point.
2. **Reputation** — store, product, and platform reviews with moderation and verified-purchase signals.
3. **Discovery** — best-rated stores, popular stores, new stores, featured products, offers, categories and richer search.
4. **Seller onboarding** — guided store creation, branding, catalog setup and progressive completion. Store registration, tenant-domain provisioning, seller authentication, store settings and catalog onboarding coverage are implemented. Seller catalog forms now provide assisted URL generation, clearer publication controls, searchable categories, lightweight product validation and an explicit catalog publication lifecycle.
5. **Marketplace commerce** — public product pages now provide a safe entry point into the owning tenant storefront/cart flow; unified cross-tenant cart, checkout, orders, payments and customer notifications remain pending.
6. **Growth** — favorites, follows, promotions, recommendations and seller analytics.

The Marketplace visual system will follow the **IntegralTec product-family direction**: professional, modern, lightweight and consistent, while keeping CombosPlus recognizable as a commerce product.

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

### Option A — native development

~~~bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
~~~

For the local development server:

~~~bash
composer run dev
~~~

### Option B — Docker + FrankenPHP (recommended local container setup)

The repository includes a Docker/FrankenPHP foundation. The `compose.yaml` development stack runs MySQL in Docker and starts Laravel Octane with the FrankenPHP server.

1. Install Docker Engine/Docker Compose and make sure Docker is running.
2. Install PHP/Composer dependencies on the host so the existing Compose/Sail runtime can be built:

~~~bash
composer install
~~~

3. Create the local environment:

~~~bash
cp .env.example .env
php artisan key:generate
~~~

4. Configure `.env` for the Docker MySQL service:

~~~dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=combosplus
DB_USERNAME=sail
DB_PASSWORD=password

TENANCY_CENTRAL_DOMAINS=localhost
~~~

The exact database credentials can be changed together with the MySQL variables used by `compose.yaml`.

**Tenant database creation:** Stancl Tenancy creates a separate MySQL database for each store. The local Sail user therefore needs global `CREATE` and `DROP` privileges. The Compose stack now installs `docker/mysql/tenant-privileges.sql` automatically when the MySQL data volume is initialized.

If the `sail-mysql` volume already existed before this fix, MySQL will not rerun initialization scripts. Apply the grant once with:

~~~bash
./vendor/bin/sail mysql -uroot -p${DB_PASSWORD} -e "GRANT CREATE, DROP ON *.* TO 'sail'@'%'; FLUSH PRIVILEGES;"
~~~

Then retry store registration. To recreate a disposable local database from scratch instead, use:

~~~bash
./vendor/bin/sail down -v
./vendor/bin/sail up -d
~~~

Do not use the local Sail privilege grant as the production database-security model. Production should use a dedicated database-management connection/user with only the privileges required by the tenancy lifecycle.

5. Start the containers:

~~~bash
./vendor/bin/sail up -d
~~~

The application is exposed on `http://localhost` by default. The container starts Octane using **FrankenPHP** rather than PHP's built-in development server.

6. Run migrations and create the storage link:

~~~bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan storage:link
~~~

7. Install/build frontend assets. For the simplest local workflow, run Vite on the host:

~~~bash
npm install
npm run dev
~~~

Or generate a production-like asset build:

~~~bash
npm run build
~~~

8. Useful Docker commands:

~~~bash
./vendor/bin/sail ps
./vendor/bin/sail logs -f laravel.test
./vendor/bin/sail artisan about
./vendor/bin/sail artisan test
./vendor/bin/sail down
~~~

### Multi-tenant local domains

CombosPlus provisions public tenant domains using the configured central domain. With `TENANCY_CENTRAL_DOMAINS=localhost`, a store with slug `demo` is intended to use:

~~~text
http://demo.localhost
~~~

Modern browsers normally resolve `*.localhost` to the local machine. If your environment does not, add the required tenant hostname to `/etc/hosts` pointing to `127.0.0.1`.

When testing seller administration, first register a store from the Marketplace, then use the generated tenant URL/session bridge to enter its Filament back office.

### FrankenPHP image directly

The repository also contains a `Dockerfile` based on `dunglas/frankenphp`. It can be built independently when testing the image itself:

~~~bash
docker build -t combosplus-frankenphp .
docker run --rm -p 8000:8000 --env-file .env combosplus-frankenphp
~~~

For normal development, prefer `compose.yaml` because it also provisions the MySQL service and persistent Docker volume.

> **Important:** the Docker image is not a substitute for application configuration. The `.env` values, central domain configuration and database availability must match the selected local deployment mode.

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
