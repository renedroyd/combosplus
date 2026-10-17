# SaaS billing lifecycle

CombosPlus keeps platform subscription billing in the central database. Tenant commerce payments remain in each tenant's operational database and are not part of this workflow.

## Manual payment review

A tenant owner or administrator submits a payment and a private receipt from `/admin`. Platform administrators review it from `/platform`. A payment starts as `pending` and can only be reviewed once.

## Subscription activation and renewal

When a pending payment is approved, the review transaction creates a SaaS subscription record linked to the tenant, selected plan price, and approved payment. Monthly and yearly prices determine the subscription period. If the tenant already has an unexpired subscription, the new period is scheduled to begin when that period ends, avoiding overlap or lost paid time.

Subscription records are historical; the current subscription is the active record whose start and end timestamps include the current time. The platform subscription list is read-only in this phase.

## Plan entitlements

The platform can configure per-plan limits and feature flags under **Planes y precios → Límites y funciones**. Each entitlement has a key and string value:
- Integer strings represent numeric limits, for example `max_products = 250`.
- `unlimited` represents no numeric cap.
- `true` and `false` represent feature flags, for example `custom_domain = true`.

The enforced limits are:
- Inicial: 100 products, 1 team user (owner included), 1 active branch.
- Profesional: 1,000 products, 5 team users (owner included), 2 active branches.
- Empresarial: unlimited products, 15 team users (owner included), 5 active branches.
- Sales and orders have no monthly quota while the subscription is active or in its seven-day grace period.

Known plan slugs receive these defaults when limit entitlements are absent. The central migration backfills the three limit entitlements for existing plans with recognized slugs, without overwriting values already configured by platform administrators. Explicit entitlement values take precedence over defaults.

## Grace period and restrictions

The grace period is seven days after `ends_at` by default and can be configured with `SAAS_GRACE_DAYS`. During grace, the last plan's entitlements remain in effect and commercial operations continue. After grace, tenant storefront and Filament mutations are blocked; read-only pages, historical data, authentication, contact and subscription-payment renewal remain accessible. Existing data is never automatically deleted when a subscription expires or a tenant downgrades.

## Enforcement

Product and branch creation check the current subscription state and the corresponding plan limit in the backend. Their quota checks are serialized using a lock on the central tenant record to reduce race conditions during concurrent creation. Team-user limits count active non-customer memberships, including the owner; customer accounts are not counted as team users. `SaasTenantAccessService::assertCanAddTeamMember()` is the common backend check for team-membership creation flows.

Use `App\Services\SaasPlanEntitlementService` to resolve the current subscription and entitlement values. `App\Services\SaasTenantAccessService` resolves active/grace/restricted access and enforces quotas.

## Operational notes

- Run the normal central migration command to create central billing tables and backfill known plan limits.
- Run the normal tenant migration command to create the `branches` table in each tenant database.
- The billing tables are central; branches and products remain tenant-local.
- No automatic card charge, invoice issuance, or scheduled expiration job is included. Access state is evaluated on each request.
