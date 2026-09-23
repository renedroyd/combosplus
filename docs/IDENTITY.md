# Identity and tenant access

CombosPlus uses a staged platform-identity model for database-per-tenant tenancy.

## Principles

- The platform owns identity and tenant membership metadata.
- Tenant databases own operational commerce data.
- A user's membership determines whether that identity can enter a tenant.
- Roles are explicit: owner, admin, manager, staff, customer.
- Tenant database isolation remains the primary security boundary; roles are an authorization layer, not a substitute for isolation.
- Central models that must work while tenancy is initialized use the CentralConnection trait.

## Membership model

The tenant_memberships table is a central table with:

- tenant_id
- user_id
- role
- status
- is_owner

A user can belong to multiple tenants, and each tenant can assign a different role to that user. The tenant_id + user_id pair is unique.

## Current transition

The existing tenant users table is retained temporarily because commerce relationships currently reference tenant-local users. This phase introduces the central membership contract without changing those operational relationships.

The next identity increment will:

1. Define the tenant-entry middleware/service.
2. Resolve the authenticated platform identity against an active membership.
3. Add safe tenant switching.
4. Add policies that reject cross-tenant object access.
5. Migrate customer/operational user relationships without breaking existing orders, carts and addresses.

## Security rules

Never authorize a tenant request from a user-controlled tenant identifier alone. Tenant context must be established by the tenancy resolver, and membership must be checked against that resolved tenant.

Never use a tenant_id column on operational commerce tables merely to emulate database-per-tenant isolation.
