# Tenant-aware authorization

Authorization is evaluated against the resolved tenant and the authenticated PlatformUser.

## Customer data

- Customers can only view and manage their own addresses.
- Customers can only view their own orders.
- Customers can only cancel their own pending orders.
- Zelle payment confirmation is protected by the same order authorization boundary.
- Staff roles can operate tenant orders according to the role matrix.

## Collection boundaries

Order listing is explicitly scoped: customers query only records whose user_id matches the authenticated platform identity. Staff roles can query the tenant's complete order collection. This is separate from object-level policies so list endpoints cannot expose another customer's records.

## Tenant isolation

All operational queries execute on the resolved tenant connection. Controllers and policies never accept a client-supplied tenant identifier as an authorization decision.

## Next

Continue moving authenticated storefront routes into the tenant route boundary, then migrate remaining customer/order/cart/address assumptions and remove compatibility paths once the tenant boundary is complete.
