# Customer identity migration

CombosPlus treats PlatformUser as the authentication identity while keeping the legacy tenant-local User as a compatibility projection for existing commerce relationships.

## Authentication boundary

- The web guard resolves PlatformUser from the central database.
- Tenant authentication requires an active tenant membership for the platform identity.
- Successful tenant login provisions or reconciles the local customer record through TenantCustomerProvisioner.
- Tenant registration creates the platform identity, creates an active customer membership for the current tenant, and provisions the tenant-local customer.
- Identity collisions are rejected when the same platform ID already exists in a tenant with a different email.

## Operational bridge

The migration deliberately keeps tenant-local users as an operational projection until every foreign-key relationship has been migrated safely.

PlatformUser exposes tenant-context relationships for cart, addresses, and orders. These relationships operate after tenant initialization, so existing checkout/profile/order flows can use the central authentication identity without adding tenant_id columns to tenant operational tables.

TenantCustomerProvisioner::sync() keeps mutable identity fields such as name, email, password and Telegram routing aligned after a profile update. This prevents the legacy projection from becoming stale while orders and notifications still depend on it.

Controllers must not replace Order::user, Address::user, Cart::user or similar relationships with a direct central PlatformUser relationship without a coordinated schema/model migration. A central model relationship from a tenant database can accidentally cross the intended database boundary.

## Authorization

Order access distinguishes customers from staff roles. Address access is restricted to the authenticated customer's own records, and address creation is policy-authorized against the current tenant.

## Migration rule

The remaining legacy User model is transitional, not a second authentication authority. New authentication decisions must use PlatformUser plus TenantMembership. Existing tenant-local relationships remain until they can be migrated with explicit tenant-local foreign keys, data backfill, compatibility handling, and isolation tests.

## Next step

Complete the operational identity migration model-by-model, starting with order/customer ownership and then cart/address/remittance relationships. Retire legacy authentication assumptions only after all dependent flows and notifications are covered by tests.
