# Tenant route boundary

The storefront is now resolved inside the tenant runtime instead of relying on central routes.

## Tenant-scoped routes

routes/tenant.php owns:

- storefront home, catalog, offers and search
- cart and checkout
- customer authentication and password reset
- profiles and addresses
- orders and Zelle payment confirmation
- remittance flows
- tenant health/secure probes

Every route in this file is behind InitializeTenancyByDomain and PreventAccessFromCentralDomains.

Authenticated operational routes additionally use auth and EnsureTenantMembership, so the resolved tenant must have an active membership for the authenticated platform identity.

## Central routes

routes/web.php is intentionally reduced to platform-level tenant switching. It must not become a second entry point for tenant operational data.

## Security boundary

Controllers should resolve operational models only after tenancy has been initialized. Authorization must use the current tenant context and never a tenant identifier supplied by the client.

This is a staged migration. The legacy tenant-local User model remains until operational relationships are fully migrated to the platform identity bridge.

## Next

Audit controllers and views for assumptions that they can execute without an initialized tenant, then migrate remaining platform-level concerns separately from tenant commerce concerns.
