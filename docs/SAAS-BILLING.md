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

Use `App\\Services\\SaasPlanEntitlementService` to resolve the current subscription and its entitlement values. This phase provides the central source of truth and resolver; individual product workflows still need to call the resolver where a limit or feature should be enforced.

## Operational notes

- Run the normal central migration command to create the new central tables.
- The two new tables are `saas_subscriptions` and `saas_plan_entitlements`.
- No automatic card charge, invoice issuance, dunning, or scheduled expiration job is included yet.
