# CombosPlus Roadmap

## Product vision

CombosPlus is evolving from a Laravel commerce application into a multi-tenant Commerce & Business Platform. The target is a premium, fast and maintainable SaaS where each business operates in an isolated tenant database.

## Current status — 2026-09-25

- **Phase 0 — Foundation:** substantially completed; CI baseline, architecture docs and project documentation are established. Remaining hardening includes smoke coverage, artifact cleanup and production configuration.
- **Phase 1 — Application architecture:** partially implemented opportunistically while securing the existing application. Policies, tenant services, safer identifiers and structured error logging are already present; formal domain boundaries and auditability remain.
- **Phase 2 — Multi-tenancy:** **foundation completed**. stancl/tenancy 3.x, landlord/tenant migrations, tenant provisioning, domain identification and isolation tests are active.
- **Phase 3 — Identity & access:** **operational identity migration completed** for carts, addresses, orders and remittances. Platform identity is now the sole operational ownership key; legacy tenant-local identity columns have been removed.
- **Phase 3 payment hardening:** remittance payment submission no longer marks a remittance as paid without trusted provider verification; submissions enter procesando and remain pending verification.
- **Phase 4 — Premium administration:** **active**. Dashboard KPI hierarchy, operational table filters, task-oriented navigation, compact record actions, admin shell density refinement, lightweight table design-system defaults and responsive/dark-mode support are implemented.
- **Marketplace migration:** **active**. Marketplace Core, Reputation, seller registration, tenant domain provisioning, seller authentication/session bridging and store settings are implemented. Seller catalog onboarding has automated coverage for category/product creation, marketplace visibility and tenant isolation. Seller catalog forms now include assisted slug generation, searchable category selection, clearer publication/inventory controls and an explicit catalog publication lifecycle.
- **Current implementation gate:** Marketplace Discovery is implemented on the working branch; verify its CI, merge only when green, verify post-merge main CI, then continue with Marketplace search/filtering.

## Delivery rules

- Every merge must include an updated README.md when the product or architecture changes.
- CI must be checked before continuing to the next implementation step.
- Tenant isolation is a security boundary and must have automated tests.
- Prefer small, reviewable phases over a large migration.
- Premium UX means hierarchy, consistency, whitespace and useful micro-interactions without unnecessary visual weight.

## Phases

### Phase 3 — Identity and access
- [x] Platform users and tenant memberships.
- [x] Tenant roles: owner, admin, manager, staff and customer.
- [x] Tenant switching foundation.
- [x] Policies and permissions for products, orders and addresses.
- [x] Secure cross-tenant URL/object access.
- [x] Central platform identity with tenant-local customer compatibility projection.
- [x] Harden order/cart/checkout identity and transaction boundaries.
- [x] Migrate operational ownership to platform_user_id for carts, addresses, orders and remittances.
- [x] Add checkout/cart cross-user and cross-tenant isolation regression coverage.
- [x] Normalize legacy order payment method data into payment_method_id.
- [x] Prevent unverified remittance submissions from transitioning directly to pagado.
- [x] Isolate remittance payment submission state transitions behind an application service.
- [x] Complete removal of legacy operational user_id columns after eliminating compatibility reads/writes.
- [x] Normalize remaining legacy order payment fields and Filament administration references.
- [ ] Replace remittance payment-state placeholder with verified provider transactions.
- [ ] Audit remittance ownership and payment-state transitions with behavior-level tests.

### Phase 4 — Premium administration
- [x] First premium dashboard KPI hierarchy.
- [x] First operational table UX pass with filters for orders, products and customers.
- [x] Redesign Filament navigation and administration information architecture.
- [x] Standardize compact record actions with accessible tooltips.
- [x] Refine admin shell density: brand/header footprint, full-width content workspace and standardized sidebar width.
- [x] Establish lightweight table design-system defaults for search, pagination and mobile behavior.
- [ ] KPIs, sales, orders, customers, products and operational alerts.
- [x] Enable responsive/dark-mode friendly administration behavior without custom CSS/JS weight.
- [x] Tenant branding/settings: store profile, public slug/domain synchronization and seller-facing store settings.
- [x] Seller onboarding: store creation, owner access, domain provisioning and catalog onboarding regression coverage.
- [x] Seller catalog UX: assisted slugs, searchable categories, clearer publication controls and basic numeric validation.

## Marketplace implementation

- [x] Marketplace Core.
- [x] Store/product/platform reputation schema and aggregate rating foundation.
- [x] Seller registration and owner membership.
- [x] Automatic tenant public-domain provisioning.
- [x] Seller authentication and central-to-tenant session bridge.
- [x] Store profile/settings and slug/domain synchronization.
- [x] Catalog onboarding regression coverage and tenant isolation.
- [x] Seller catalog form UX refinement.
- [x] Explicit draft/unpublished/published catalog state model.
- [x] Marketplace discovery: featured/new stores, featured products and popular categories; offers remain pending the promotion model.
- [x] Marketplace search by store name/description.
- Marketplace product discovery with text search, category filtering and price/rating/newest sorting.
- [x] Marketplace category/filter navigation and richer product search.
- [ ] Marketplace product pagination/caching for larger catalogs.
- [ ] Marketplace commerce across tenants.

## Later phases

- Phase 5 — Store 2.0: conversion-focused storefront, catalog, offers and performance.
- Phase 6 — CRM: customer profiles, segmentation and activity.
- Phase 7 — Marketing: coupons, campaigns and recovery flows.
- Phase 8 — Payments: gateway abstraction and verified provider transactions.
- Phase 9 — Analytics: revenue, conversion and product performance.
- Phase 10 — SaaS platform: plans, subscriptions and entitlements.
- Phase 11 — White-label: branding and custom domains.
- Phase 12 — Scale and operations: queues, Horizon, Octane, backups and observability.

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

## Local Docker + FrankenPHP development

- `compose.yaml` provides the local MySQL service and starts Laravel Octane with the FrankenPHP server.
- `.env` should use `DB_CONNECTION=mysql` and `DB_HOST=mysql` when the application runs inside the Compose network.
- Start with `./vendor/bin/sail up -d`, then run migrations and tests through `./vendor/bin/sail artisan ...`.
- `Dockerfile` is based on `dunglas/frankenphp` for direct image builds when needed.
- Detailed commands and tenant-domain testing notes are documented in the README.

## CI checkpoints

- Main CI #146 / run 36165705593: success after Marketplace catalog onboarding merge.
- PR #57: merged after CI success; main commit d4931bd69b5f3b563dfbeef246f55f85210b32d2.
- Main CI #146: success after PR #54 seller admin session bridge.
- PR #55: merged after correcting Filament 5 navigation type and central-connection persistence/notification handling.
- PR #56: merged after tenant slug/domain synchronization and collision validation.
- PR #57: merged after catalog onboarding regression coverage and tenant isolation tests.
- PR #61 Marketplace Discovery: CI check run 108229444897 / workflow 36182941739: success.
- The direct GitHub Check Runs endpoint is the operational source of truth when specialized status/check wrappers return empty arrays.
