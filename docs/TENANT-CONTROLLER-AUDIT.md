# Tenant controller audit

This audit follows the storefront move into the tenant route boundary.

## Fixed runtime gaps

- Product detail now has a controller action and tenant storefront view.
- Remittance calculator route now matches the controller API through `calcularCosto`.
- Product filters normalize multi-value inputs before passing them to query builders.
- Product listing pagination preserves filter query parameters.

## Tenant boundary

The audited storefront controllers execute after tenancy has been initialized by `routes/tenant.php`. Product, category, cart, order, address, payment and remittance models therefore resolve against the tenant connection where their tables are tenant-owned.

Authenticated customer operations continue using the central `PlatformUser` identity while the tenant-local customer projection remains the operational bridge.

## Remaining migration work

The next identity migration should remove the remaining dependency on the legacy tenant-local `User` model from operational relationships and notifications. This requires coordinated schema, model, authentication and test changes rather than a controller-only refactor.

