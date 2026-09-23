# Authorization

CombosPlus uses two authorization layers:

1. tenant resolution and database isolation establish the security boundary;
2. policies enforce role permissions inside the resolved tenant.

Catalog management is available to owner, admin and manager roles. Staff and customer roles do not receive product create, update or delete permissions.

Order operational actions are available to owner, admin, manager and staff. Destructive order management is limited to owner, admin and manager.

A membership from tenant A is never treated as permission for tenant B. Policy checks resolve the current tenant from the tenancy context rather than accepting a tenant identifier supplied by the client.

The authenticated web guard resolves PlatformUser. Customer ownership rules for legacy tenant-local users remain part of the staged identity migration and will be completed when operational customer relationships are migrated.
