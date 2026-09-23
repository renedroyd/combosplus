# Tenancy migration strategy

CombosPlus is moving to database-per-tenant multi-tenancy with `stancl/tenancy` 3.x.

## Ownership model

### Central database

The central database owns platform-level metadata:

- tenants
- domains
- future platform identities and memberships
- future plans, subscriptions and feature flags

### Tenant databases

Each tenant database will own operational business data:

- users/customer accounts
- products and categories
- offers
- carts and checkout state
- orders and order items
- addresses
- payment methods and payment records
- remittances
- tenant settings and operational data

## Migration sequence

1. Install and register `stancl/tenancy`.
2. Create the central `tenants` and `domains` tables.
3. Configure database-per-tenant bootstrapping and domain identification.
4. Move business migrations to `database/migrations/tenant`.
5. Provision a tenant and verify its database is created and migrated.
6. Verify tenant A cannot access tenant B operational data.
7. Move authentication and authorization toward platform identity + tenant membership.
8. Enable tenant-aware Filament/storefront routing.
9. Add tenant-specific cache, filesystem and queue isolation as the infrastructure moves to Redis.

## Important transition rule

Do not add `tenant_id` columns to operational tables just to make the current schema multi-tenant. Database-per-tenant is the isolation boundary. Shared resources will be explicitly identified as central resources instead.

## Local smoke test

After dependencies are installed and migrations are available:

    php artisan migrate
    php artisan tinker

Then create a tenant and domain:

    $tenant = App\\Models\\Tenant::create(['id' => 'demo']);
    $tenant->domains()->create(['domain' => 'demo.localhost']);

The package lifecycle should create the tenant database and run migrations from `database/migrations/tenant`.

## Production database permissions

The central database credentials must be able to create/drop tenant databases (or use a restricted database-management account). This will be documented and hardened before production rollout.
