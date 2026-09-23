# Customer identity migration

CombosPlus currently has a platform identity (PlatformUser) and a legacy tenant-local User record because commerce tables still reference local user IDs.

TenantCustomerProvisioner provides the controlled bridge:

- it requires an initialized tenant context;
- it creates the tenant-local customer with the platform user's identifier;
- it copies the display identity and password hash without authenticating against the tenant database;
- it reuses an existing local customer only when the email identity matches;
- it rejects an identifier collision with a different email.

This is intentionally an incremental migration boundary. Authentication remains platform-level while operational relationships continue using tenant-local customer records.

The next migration step is to integrate this provisioner into tenant-domain authentication flows and registration, then migrate order/cart/address relationships without changing the tenant isolation boundary.
