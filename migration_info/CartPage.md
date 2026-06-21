# CartPage (`/cart`)

**File:** `src/Pages/CartPage/CartPage.jsx`

---

## What It Does

Displays the logged-in buyer's **shopping cart** — all `cartitem` rows for their `buyer_id`. Users can adjust quantities, remove items, select specific items, and proceed to checkout for selected items only.

---

## URL Interaction

- **Static route** `/cart` — no URL params.
- "Continue Shopping" links to `/search`.
- **"Buy Now"** navigates to:
  ```
  /product/confirm_order?cartItems=id1,id2,id3
  ```
  where IDs are comma-separated `cart_item_id`s of selected items.

---

## Supabase Queries

### Fetch cart items (on mount)
```sql
SELECT cart_item_id,
  buyer (is_deleted),
  quantity,
  product:product_id (product_id, name, price, description, quantity,
    product_image (image_url)
  )
FROM cartitem
WHERE buyer_id = <userId>
```

### Update quantity
```sql
UPDATE cartitem SET quantity = <newQty> WHERE cart_item_id = <id>
```

### Remove item
```sql
DELETE FROM cartitem WHERE cart_item_id = <id>
```

---

## What It Can Do

1. **View all cart items** — image, name, description, individual price, line total.
2. **Adjust quantity** — +/- buttons, with stock validation (can't exceed `product.quantity`).
3. **Remove items** — delete button per item.
4. **Select/deselect items** — checkbox per item, "Select All Available" header toggle.
5. **Stock-aware selection** — out-of-stock or over-capacity items are disabled with red warning.
6. **Buy selected items** — validates stock again, navigates to checkout with comma-separated IDs.
7. **Guard: deleted buyer** — if `buyer.is_deleted`, shows "Buyer Account is Deleted" screen.
8. **Empty cart** — shows an illustration with "Start Shopping" button.
9. **Cart total** — shows total for all items and selected items.

---

## Typical UI

- Full-width cart layout under the NavBar (with `mt-20` spacing).
- Header with shopping bag icon + "Your Cart" title.
- "Select All Available" bar with counts (selected/selectable).
- Each cart item is a row: checkbox → image → name/description/price → quantity stepper → line total + delete button.
- Unavailable items shown with red background and warning text.
- Bottom bar: "Continue Shopping" button, selected total, "Buy Now (N items)" button.
- Loading skeleton when fetching.
- Empty state with cart icon + "Discover amazing products" CTA.
