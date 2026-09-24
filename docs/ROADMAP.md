# CombosPlus Roadmap

## Product vision

CombosPlus is evolving from a Laravel commerce application into a multi-tenant Commerce & Business Platform. The target is a premium, fast and maintainable SaaS where each business operates in an isolated tenant database.

## Current status — 2026-09-24

- **Phase 0 — Foundation:** substantially completed; CI baseline, architecture docs and project documentation are established. Remaining hardening includes smoke coverage, artifact cleanup and production configuration.
- **Phase 1 — Application architecture:** partially implemented opportunistically while securing the existing application. Policies, tenant services, safer identifiers and structured error logging are already present; formal domain boundaries and auditability remain.
- **Phase 2 — Multi-tenancy:** **foundation completed**. `stancl/tenancy 3.x`, landlord/tenant migrations, tenant provisioning, domain identification and isolation tests are active.
- **Phase 3 — Identity & access:** **operational identity migration completed for carts, addresses and orders**. Platform identity is now the primary ownership key for these flows, while legacy tenant-local identity columns remain temporarily for compatibility. Checkout/cart isolation coverage is in place.
- **Current hardening:** remaining Phase 3 work is focused on legacy schema cleanup, remittance ownership/payment-state audit and completing the compatibility migration without weakening tenant isolation.
- **Next gate:** finish the remaining Phase 3 schema/payment audits, then begin Phase 4 premium administration while continuing security/schema audits.

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
- [~] Introduce clear domain/application boundaries.
- [~] Extract business operations into Actions/Services where useful.
- [~] Add Policies, Events and Jobs for cross-cutting workflows.
- [x] Replace fragile order/remittance identifiers such as `uniqid()` with collision-safe random identifiers.
- [~] Add auditability and structured logging.

### Phase 2 — Multi-tenancy
- [x] Install and configure `stancl/tenancy`.
- [x] Create landlord/platform database.
- [x] Create tenant database provisioning lifecycle.
- [x] Configure tenant identification by domain.
- [x] Separate landlord and tenant migrations.
- [x] Establish tenant commerce schema for products, catalog, carts, orders, payments and operational data.
- [x] Add tenant isolation and provisioning tests.

### Phase 3 — Identity and access
- [x] Platform users and tenant memberships.
- [x] Tenant roles: owner, admin, manager, staff and customer.
- [x] Tenant switching foundation.
- [x] Policies and permissions for products, orders and addresses.
- [x] Secure cross-tenant URL/object access.
- [x] Move storefront/customer routes behind tenant resolution and membership middleware where authentication is required.
- [x] Central platform identity with tenant-local customer compatibility projection.
- [x] Harden order/cart/checkout identity and transaction boundaries.
- [x] Align order address relationships with tenant schema.
- [x] Align tenant address schema with storefront/controller fields.
- [x] Migrate operational ownership to `platform_user_id` for carts, addresses and orders.
- [x] Add checkout/cart cross-user and cross-tenant isolation regression coverage.
- [ ] Complete removal of legacy operational `user_id` columns after all compatibility reads/writes are eliminated.
- [ ] Normalize remaining legacy order/payment fields and relationships.
- [ ] Audit remittance ownership/payment state transitions.

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
- [ ] Replace remittance's current payment-state placeholder with verified provider transactions.

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

## CI checkpoints

- Main CI #81: success after operational identity migration.
- Main CI #77: success after checkout/cart isolation fixes.
- Main CI #73: success after roadmap/status update.
- Main CI #71: success after address schema alignment.
- Pull-request CI #79: failed during the operational identity migration and was corrected before merge.
- The GitHub Actions runs endpoint is the operational source of truth when specialized status/check wrappers return empty arrays.
