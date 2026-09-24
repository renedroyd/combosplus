# CombosPlus Roadmap

## Product vision

CombosPlus is evolving from a Laravel commerce application into a multi-tenant Commerce & Business Platform. The target is a premium, fast and maintainable SaaS where each business operates in an isolated tenant database.

## Current status — 2026-09-24

- **Phase 0 — Foundation:** substantially completed; CI baseline, architecture docs and project documentation are established. Remaining hardening includes smoke coverage, artifact cleanup and production configuration.
- **Phase 1 — Application architecture:** partially implemented opportunistically while securing the existing application. Policies, tenant services, safer identifiers and structured error logging are already present; formal domain boundaries and auditability remain.
- **Phase 2 — Multi-tenancy:** **foundation completed**. stancl/tenancy 3.x, landlord/tenant migrations, tenant provisioning, domain identification and isolation tests are active.
- **Phase 3 — Identity & access:** **operational identity migration completed for carts, addresses, orders and remittances**. Platform identity is now the primary ownership key for these flows, while legacy tenant-local identity columns remain temporarily for compatibility.
- **Phase 3 payment hardening:** remittance payment submission no longer marks a remittance as paid without trusted provider verification; submissions enter procesando and remain pending verification.
- **Latest CI checkpoint:** PR #29 passed CI and was merged; Main CI #87 is green on commit bb65c73435c34b67e6c04104293b9b0dc1ed7c4a.
- **Current hardening:** remaining Phase 3 work is focused on eliminating legacy operational reads/writes, normalizing remaining payment relationships and replacing transitional payment-state behavior with a provider-backed transaction abstraction.
- **Next gate:** finish the remaining Phase 3 schema/payment audits, then continue Phase 4 premium administration while maintaining tenant isolation and security coverage.

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
- [ ] Complete removal of legacy operational user_id columns after all compatibility reads/writes are eliminated.
- [ ] Normalize remaining legacy payment fields and relationships.
- [ ] Replace remittance payment-state placeholder with verified provider transactions.
- [ ] Audit remittance ownership and payment-state transitions with behavior-level tests.

### Phase 4 — Premium administration
- [ ] Redesign Filament dashboard.
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

- Main CI #87: success after PR #29 merge; remittance identity/payment-state hardening.
- PR CI #86: success for PR #29.
- Main CI #81: success after operational identity migration.
- Main CI #77: success after checkout/cart isolation fixes.
- Main CI #71: success after address schema alignment.
- PR CI #79: failed during the operational identity migration and was corrected before merge.
- The GitHub Actions runs endpoint is the operational source of truth when specialized status/check wrappers return empty arrays.
