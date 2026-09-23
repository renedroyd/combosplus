# CombosPlus Roadmap

## Product vision

CombosPlus is evolving from a Laravel commerce application into a multi-tenant Commerce & Business Platform. The target is a premium, fast and maintainable SaaS where each business operates in an isolated tenant database.

## Delivery rules

- Every merge must include an updated `README.md` when the product or architecture changes.
- CI must be checked before continuing to the next implementation step.
- Tenant isolation is a security boundary and must have automated tests.
- Prefer small, reviewable phases over a large migration.
- Premium UX means hierarchy, consistency, whitespace and useful micro-interactions without unnecessary visual weight.

## Phases

### Phase 0 — Foundation and stabilization
- [x] Repository and stack audit.
- [x] Establish product/architecture documentation.
- [x] Establish GitHub Actions baseline.
- [x] Replace the Laravel starter README with project documentation.
- [ ] Expand automated application smoke tests.
- [ ] Remove starter/duplicate artifacts.
- [ ] Normalize production configuration and Docker workflow.

### Phase 1 — Application architecture
- [ ] Introduce clear domain/application boundaries.
- [ ] Extract business operations into Actions/Services where useful.
- [ ] Add Policies, Events and Jobs for cross-cutting workflows.
- [ ] Replace fragile identifiers such as `uniqid()` with ULID/UUID-based identifiers where appropriate.
- [ ] Add auditability and structured logging.

### Phase 2 — Multi-tenancy
- [ ] Install and configure `stancl/tenancy`.
- [ ] Create landlord/platform database.
- [ ] Create tenant database provisioning lifecycle.
- [ ] Configure tenant identification by domain/subdomain.
- [ ] Separate landlord and tenant migrations.
- [ ] Migrate products, catalog, carts, orders, payments and operational data into tenant databases.
- [ ] Add tenant isolation and provisioning tests.

### Phase 3 — Identity and access
- [ ] Platform users and tenant memberships.
- [ ] Tenant roles: owner, admin, manager, staff and customer.
- [ ] Tenant switching.
- [ ] Policies and permissions.
- [ ] Secure cross-tenant URL/object access.

### Phase 4 — Premium administration
- [ ] Redesign Filament dashboard.
- [ ] KPIs, sales, orders, customers, products and operational alerts.
- [ ] Consistent design system.
- [ ] Responsive and dark-mode friendly interface.
- [ ] Tenant branding/settings.

### Phase 5 — Store 2.0
- [ ] Conversion-focused home page.
- [ ] Catalog, search, filters and product detail.
- [ ] Offers and promotions.
- [ ] Cart and frictionless checkout.
- [ ] Trust, benefits and credibility sections.
- [ ] Performance and image optimization.

### Phase 6 — CRM
- [ ] Customer profiles and purchase history.
- [ ] Segmentation and notes.
- [ ] Addresses and customer activity.

### Phase 7 — Marketing
- [ ] Coupons and promotions.
- [ ] Campaigns.
- [ ] Abandoned cart recovery.
- [ ] Cross-sell and up-sell.

### Phase 8 — Payments
- [ ] Payment gateway abstraction.
- [ ] Zelle adapter.
- [ ] Additional gateway adapters without coupling the domain to a provider.

### Phase 9 — Analytics
- [ ] Revenue and order KPIs.
- [ ] Average order value.
- [ ] Conversion and repeat customer metrics.
- [ ] Product/category performance.

### Phase 10 — SaaS platform
- [ ] Plans and subscriptions.
- [ ] Trials.
- [ ] Feature flags/entitlements.
- [ ] Tenant lifecycle and billing states.

### Phase 11 — White-label
- [ ] Logo, favicon and brand colors.
- [ ] Custom domains.
- [ ] Tenant email/notification settings.
- [ ] Legal/business information.

### Phase 12 — Scale and operations
- [ ] Redis and queue strategy.
- [ ] Horizon.
- [ ] Octane/FrankenPHP optimization.
- [ ] Per-tenant backups.
- [ ] Observability and rate limiting.
- [ ] Object storage/CDN where useful.

## Target stack

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
