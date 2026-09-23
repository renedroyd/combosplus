# Architecture Direction

## Tenancy model

CombosPlus will use database-per-tenant tenancy.

The platform/landlord database owns platform concerns such as:

- tenants
- tenant domains
- platform identity and memberships
- plans/subscriptions
- platform configuration

Each tenant database owns business data such as:

- users/customers as appropriate
- products and categories
- offers
- carts
- orders and order items
- payments
- addresses
- tenant operational settings

## Identity

The preferred model is a platform-level identity plus tenant memberships rather than adding a single `tenant_id` column to every existing table. This permits one person to belong to multiple businesses and switch tenant context securely.

## Tenant resolution

Initial resolution will support subdomains/domains. Custom domains can be enabled later without changing the business domain model.

## Security boundary

Tenant context must be initialized before tenant-scoped queries execute. Automated tests must prove that data from tenant A cannot be read, modified or exposed through tenant B.

## Application layers

The target structure separates:

- HTTP/UI
- application actions/services
- domain/business rules
- persistence/models
- infrastructure integrations

Not every class needs an abstraction. The goal is explicit boundaries around business-critical workflows, not ceremony.

## Async work

Notifications, emails, analytics aggregation and external integrations should use jobs/events when they do not need to block the request.

## Performance

Keep the public store lightweight. Use caching and optimized queries deliberately, avoid N+1 queries, optimize images, and use queues for non-critical work. Octane/FrankenPHP should improve throughput without forcing application complexity where it is not needed.

## UI

Admin and storefront have different jobs:

- Admin: operational density, clarity and fast actions.
- Storefront: trust, product discovery, conversion and low friction.

A shared design system should provide consistency without making both interfaces look identical.
