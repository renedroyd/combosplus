-- Stancl Tenancy creates one database per tenant.
-- The local Sail application user therefore needs global CREATE/DROP privileges.
-- Keep this limited to local development; production should use a dedicated
-- privileged database-management connection/user instead.
GRANT CREATE, DROP ON *.* TO 'sail'@'%';
FLUSH PRIVILEGES;
