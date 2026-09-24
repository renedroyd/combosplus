# CombosPlus Roadmap

## Product vision

CombosPlus is evolving from a Laravel commerce application into a multi-tenant Commerce & Business Platform. The target is a premium, fast and maintainable SaaS where each business operates in an isolated tenant database.

## Current status — 2026-09-24

- **Phase 0 — Foundation:** substantially completed; CI baseline, architecture docs and project documentation are established. Remaining hardening includes smoke coverage, artifact cleanup and production configuration.
- **Phase 1 — Application architecture:** partially implemented opportunistically while securing the existing application. Policies, tenant services, safer identifiers and structured error logging are already present; formal domain boundaries and auditability remain.
- **Phase 2 — Multi-tenancy:** **foundation completed**. stancl/tenancy 3.x, landlord/tenant migrations, tenant provisioning, domain identification and isolation tests are active.
- **Phase 3 — Identity & access:** **operational identity migration completed** for carts, addresses, orders and remittances. Platform identity is now the sole operational ownership key; legacy tenant-local identity columns have been removed.
- **Phase 3 payment hardening:** remittance payment submission no longer marks a remittance as paid without trusted provider verification; submissions enter procesando and remain pending verification.
- **Phase 4 — Premium administration:** **active**. Dashboard KPI hierarchy, operational table filters, task-oriented navigation and compact record actions are implemented.
- **Latest implementation checkpoint:** PR #38 passed PR CI #106 and was merged into main at commit b9ef96963de9f877aecc1359d6ca07fce77c749d; main CI #107 is green.
- **Current hardening:** remaining Phase 3 work is focused on replacing transitional remittance payment-state behavior with a provider-backed transaction abstraction and completing behavior-level payment audits.
- **Next gate:** continue Phase 4 with the broader design system, responsive/dark-mode refinements and tenant branding while maintaining tenant isolation and security coverage.

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
- [ ] KPIs, sales, orders, customers, products and operational alerts.
- [ ] Consistent design system.
- [ ] Responsive and dark-mode friendly interface.
- [ ] Tenant branding/settings.

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
