-- Stancl Tenancy creates one database per tenant.
-- The local Sail application user needs permission to create/drop tenant
-- databases and to run migrations/read/write data inside them.
-- Keep this limited to local development; production should use a dedicated
-- privileged database-management connection/user instead.
GRANT CREATE, DROP ON *.* TO 'sail'@'%';
GRANT ALL PRIVILEGES ON `tenant_%`.* TO 'sail'@'%';
FLUSH PRIVILEGES;
