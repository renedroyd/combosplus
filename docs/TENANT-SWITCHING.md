# Tenant switching

The platform identity can belong to multiple tenants.

Tenant switching follows these rules:

- only authenticated users can request the membership list;
- only active memberships are returned;
- switching requires an active membership for the destination tenant;
- the destination domain is read from central tenant metadata;
- the destination tenant is never selected from untrusted domain input;
- tenants without a configured domain fail with HTTP 409;
- the target URL is generated from the current request scheme.

The switch endpoint is an entry point to the tenant domain. Tenant authorization remains enforced by EnsureTenantMembership after tenancy initialization.

A future production increment will move switching into a dedicated UI and establish shared platform-session/domain configuration for seamless navigation across tenant domains.
