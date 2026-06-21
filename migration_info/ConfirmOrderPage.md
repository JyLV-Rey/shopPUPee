# ConfirmOrderPage (`/product/confirm_order`)

**File:** `src/Pages/BuyPage/ConfirmOrderPage.jsx`

---

## What It Does

The **checkout / order confirmation page**. Handles two flows:
1. **Single product buy** — from "Buy Now" on a product page (`productId` + `quantity` params).
2. **Multi-item cart buy** — from cart "Buy Now" (`cartItems` = comma-separated `cart_item_id`s).

Users select a **delivery address**, **payment method**, and **courier service**, then confirm the order.

---

## URL Interaction

**Two mutually exclusive query-param modes:**
```
/product/confirm_order?productId=<id>&quantity=<n>     (single product)
/product/confirm_order?cartItems=id1,id2,id3            (from cart)
```

On success, navigates to:
```
/product/view_receipt?orderId=<new_order_id>&justOrdered=true
```

---

## Supabase Queries

### Fetch single product
```sql
SELECT product_id, name, price, description,
  product_image (image_url),
  seller (seller_id, seller_name,
    address (street, city, postal_code)
  )
FROM product
WHERE product_id = <productId>
```

### Fetch cart items (for multi-item flow)
```sql
SELECT cart_item_id, quantity,
  product:product_id (product_id, name, price, description,
    product_image (image_url),
    seller (seller_id, seller_name,
      address (street, city, postal_code)
    )
  )
FROM cartitem
WHERE cart_item_id IN (<id1, id2, ...>)
```

### Order creation (transaction-like sequence)
1. **Insert order:**
   ```sql
   INSERT INTO order (buyer_id, status) VALUES (<userId>, 'Pending')
   RETURNING order_id
   ```
2. **Insert order items:**
   ```sql
   INSERT INTO order_item (order_id, product_id, quantity)
   VALUES (...), (...), ...
   ```
3. **Decrease product quantities** via RPC:
   ```sql
   SELECT decrease_quantity(<productId>, <quantity>)
   ```
4. **Insert payment:**
   ```sql
   INSERT INTO payment (order_id, payment_method, payment_status)
   VALUES (<orderId>, <method>, <'Pending'|'Success'>)
   RETURNING payment_id
   ```
   - If COD → `paid_at` is explicitly set to `null`.
5. **Insert delivery:**
   ```sql
   INSERT INTO delivery (order_id, delivery_status, courier_service, buyer_address_id)
   VALUES (<orderId>, 'Preparing', <courier>, <addressId>)
   ```
6. **Delete cart items** (if from cart):
   ```sql
   DELETE FROM cartitem WHERE cart_item_id IN (<ids>)
   ```

---

## What It Can Do

1. **Display order summary** — images, names, quantities, prices, line totals.
2. **Show seller info** — store name + address for each unique seller.
3. **Delivery address selection** — uses `AddressBook` component to pick from saved addresses.
4. **Payment method** — COD, Credit/Debit Card, Digital Wallet (3-button selector).
5. **Courier service** — dropdown with 7 options (J&T, GoGo Xpress, Entrego, 2GO, JRS, Ninja Van, LBC).
6. **Confirm order** — creates order, updates stock, records payment + delivery, clears cart items.
7. **Buyer deleted guard** — checks `buyer.is_deleted` on mount and redirects to `/` with alert.
8. **Loading states** — skeleton during data fetch, "Processing..." on confirm button.
9. **Error handling** — displays error card with "Go to Search" button on failure.

---

## Typical UI

- Content under NavBar with `pt-20` padding.
- Header: checkmark icon + "Confirm Your Order" title.
- Sections stacked vertically:
  1. **Order Details** — item rows (image, name, description, seller, price×qty, line total) + total
  2. **Seller Information** — list of unique sellers with addresses
  3. **Delivery Address** — AddressBook component
  4. **Payment Method** — 3 card-style buttons
  5. **Courier Service** — dropdown select
- Large "Confirm Order - ₱Total" button at bottom.
