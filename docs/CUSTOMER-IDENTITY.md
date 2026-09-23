# Customer identity migration

CombosPlus now treats PlatformUser as the authentication identity while keeping the legacy tenant-local User as a compatibility projection for existing commerce relationships.

## Authentication boundary

- The web guard resolves PlatformUser from the central database.
- Tenant authentication can only proceed when the current tenant has an active tenant_memberships record for the platform identity.
- A successful tenant login provisions or reconciles the local customer record through TenantCustomerProvisioner.
- A tenant registration creates the platform identity, creates an active customer membership for the current tenant, and provisions the tenant-local customer.
- Identity collisions are rejected when the same platform ID already exists in a tenant with a different email.

The controllers remain usable on the existing central storefront while tenant-domain routing is migrated incrementally. They do not trust a tenant ID supplied by the client.

## Operational bridge

PlatformUser exposes tenant-context relationships for cart, addresses, and orders. These relationships are resolved against the active tenant connection. This lets existing checkout/profile/order flows continue using the central authentication identity without adding tenant_id columns to operational tables.

## Authorization

Order access distinguishes customers from staff roles: customers can view and cancel only their own pending orders, while owner/admin/manager/staff retain operational order access. Address access is restricted to the authenticated customer's own records.

Policies resolve through Laravel's service container, so TenantAccessService is injected through policy constructors. Laravel documents constructor dependency injection for policies and standard policy auto-discovery.

## Next migration step

Move the authenticated storefront and customer routes into the tenant route boundary, then remove the remaining assumptions that the central database owns operational commerce data. The legacy tenant-local User model can be retired only after those relationships and authentication flows are fully migrated.
