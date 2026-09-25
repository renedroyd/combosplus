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
- **Latest implementation checkpoint:** PR #44 passed PR CI #118 and was merged into main at commit 3986ec6d119634151274d0932c75306541774d49.
- **Current hardening:** remaining Phase 3 work is focused on replacing transitional remittance payment-state behavior with a provider-backed transaction abstraction and completing behavior-level payment audits.
- **Marketplace migration:** **active**. Marketplace Core, Reputation, seller registration, tenant domain provisioning, seller authentication/session bridging and store settings are implemented. The seller catalog onboarding flow now has automated coverage for category/product creation, publishing visibility and tenant isolation.
- **Next gate:** merge the catalog onboarding regression coverage after CI success, verify post-merge main CI, then continue with seller-facing catalog UX and marketplace discovery while maintaining tenant isolation and security coverage.

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

## CI checkpoints

- Main CI #117: success after Phase 4 table design-system documentation checkpoint.
- PR CI #118: success for responsive/dark-mode support.
- PR #44: merged after PR CI #118 success.
- Main CI #115: success after Phase 4 table design-system defaults.
- PR CI #114: success for standardized premium table UX.
- PR #42: merged after PR CI #114 success.
- Main CI #111: success after merging Phase 4 admin layout density refinement.
- PR CI #110: success for premium admin layout density.
- PR #40: merged after PR CI #110 success.
- Main CI #107: success after merging Phase 4 compact table actions.
- PR CI #106: success for standardized compact table actions.
- PR #38: merged after PR CI #106 success.
- Main CI #105: success after Phase 4 navigation/information architecture.
- PR #37: merged after PR CI #104 success.
- Main CI #99: success for Phase 4 operational table UX before merge.
- PR #35: merged after CI #99 success.
- Main CI #96: success after Phase 4 dashboard KPI implementation.
- PR CI #95: success for the first Phase 4 dashboard implementation.
- Main CI #94: success after removal of legacy operational identity fields.
- PR CI #93: success after removal of legacy operational identity fields.
- Main CI #91: success after remittance payment submission service.
- Main CI #87: success after remittance identity/payment-state hardening.
- Main CI #81: success after operational identity migration.
- Main CI #77: success after checkout/cart isolation fixes.
- Main CI #71: success after address schema alignment.
- PR CI #79: failed during the operational identity migration and was corrected before merge.
- The GitHub Actions runs endpoint is the operational source of truth when specialized status/check wrappers return empty arrays.
