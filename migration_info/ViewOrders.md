# ViewOrders (`/orders`)

**File:** `src/Pages/ViewOrders/ViewOrders.jsx`  
**Components:** `EachOrder`, `OrderFilterBar`, `ViewOrder`

---

## What It Does

Displays all orders placed by a specific buyer, sorted newest-first. Each order card shows an abbreviated summary; clicking navigates to the full receipt.

---

## URL Interaction

**Query param:** `buyerId`
```
/orders?buyerId=<id>
```

---

## Supabase Queries

```sql
SELECT * FROM "order"
WHERE buyer_id = <buyerId>
ORDER BY ordered_at DESC
```

The `EachOrder` child component then fetches further details per order (order items, products, images, delivery status).

---

## What It Can Do

1. List all orders for a buyer, most recent first.
2. Each order card links to `/product/view_receipt?orderId=<id>`.
3. Empty state with "Start Shopping" link when no orders exist.
4. Loading state with spinner text.

---

## Typical UI

- Shopping bag icon + "View Orders" header.
- Filter bar at top.
- Flex-wrap row of order cards, each showing: order ID, date, status, item count, total.
- Loading: "Loading your orders..." text.
- Empty: illustration + "No orders yet" + "Start Shopping" button.
