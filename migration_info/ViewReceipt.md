# ViewReceipt (`/product/view_receipt`)

**File:** `src/Pages/ViewReceipt/ViewReceipt.jsx`  
**Components:** `HeaderConfirmation`, `OrderInfoCard`, `CustomerInfoCard`, `OrderItemsList`, `DeliveryInfoCard`, `AddressCard`, `SellerAddressCard`, `PaymentInfoCard`, `LoadingSkeleton`, `OrderNotFoundCard`, `ProcessingNotice`

---

## What It Does

Displays the **order receipt** after purchase — a full summary of the order: buyer info, items, delivery status, payment status, seller address, and refund/cancellation actions. Also renders refund or cancellation info if the order has been refunded/cancelled.

---

## URL Interaction

**Query params:**
```
/product/view_receipt?orderId=<id>&justOrdered=true
```
- `orderId` (required, numeric) — the order to display.
- `justOrdered` (optional) — if `"true"`, shows a green confirmation banner at the top.

---

## Supabase Queries

### Main fetch (on mount, fired in parallel)

**Order info:**
```sql
SELECT order_id, is_deleted, status, ordered_at,
  buyer (first_name, last_name, email),
  order_item (quantity,
    product (product_id, name, price, description,
      product_image (image_url),
      seller (seller_name,
        address (unit_floor, postal_code, street, barangay, province, city, region)
      )
    )
  )
FROM order
WHERE order_id = <orderId>
```

**Delivery info:**
```sql
SELECT delivery_id, delivery_status, courier_service, tracking_number,
  delivery_date, buyer_address_id,
  address (unit_floor, postal_code, street, barangay, province, city, region)
FROM delivery
WHERE order_id = <orderId>
```

**Payment info:**
```sql
SELECT payment_id, payment_method, payment_status, paid_at
FROM payment
WHERE order_id = <orderId>
```

**Refund info:**
```sql
SELECT refund_reason, processed_at, refund_status
FROM refund
WHERE order_id = <orderId>
```

**Cancel info:**
```sql
SELECT cancel_reason, cancel_date
FROM cancel
WHERE order_id = <orderId>
```

---

## What It Can Do

1. **Receipt display** — order ID, status, date, items, prices, totals.
2. **Buyer info** — name, email from `buyer` relation.
3. **Delivery info card** — courier, tracking, status, expected date.
4. **Address card** — full delivery address breakdown.
5. **Seller address card** — seller's registered address.
6. **Payment info card** — method, status, paid timestamp.
7. **Processing notices** — shown if delivery or payment data is missing (order still processing).
8. **Header confirmation** — green banner when `justOrdered=true` ("Order Placed Successfully!").
9. **Cancel order** — only enabled when `delivery_status === "Preparing"`. Prompts for a reason, then:
   - Updates order → "Cancelled"
   - Updates delivery → "Cancelled"
   - Updates payment → "Cancelled"
   - Inserts into `cancel` table
   - Restores stock for each product
10. **Refund order** — only enabled when `delivery_status === "Delivered"`. Prompts for a reason, then:
    - Updates payment → "Refunded"
    - Updates delivery → "Returned"
    - Updates order → "Refunded"
    - Inserts into `refund` table
11. **Refund/cancellation info** — if refund or cancel data exists, shows a red-bordered card with reason, date, and status.
12. **Deleted order guard** — if `order.is_deleted`, shows "Order has Been Deleted."

---

## Typical UI

- Two-column grid at top: OrderInfoCard + CustomerInfoCard.
- Full-width OrderItemsList (product rows with images, quantities, prices).
- Two-column grid: DeliveryInfoCard + AddressCard.
- SellerAddressCard.
- PaymentInfoCard (or ProcessingPaymentNotice).
- Cancel/Refund buttons at bottom-right, enabled/disabled based on delivery status.
- Red dashed border card if refund/cancel exists.
