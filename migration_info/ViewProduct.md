# ViewProduct (`/product/view`)

**File:** `src/Pages/ViewProduct/ViewProduct.jsx`

---

## What It Does

Displays a **single product** in full detail: multiple images with thumbnails, price, description, seller info, star rating, reviews collapsible list, price history, monthly stats, and action buttons (Buy Now, Add to Cart, Edit).

---

## URL Interaction

**Query param:** `productId` (required, numeric)
```
/product/view?productId=42
```

If `productId` is invalid or the product is deleted/hard-deleted, renders an error state.

---

## Supabase Queries

### Main product fetch (on mount)
```sql
SELECT product_id, name, description, category, price, quantity,
  product_image (image_url),
  seller (seller_id, seller_name, is_deleted,
    address (street, city, postal_code),
    buyer (buyer_id)
  ),
  review (rating, comment, created_at,
    buyer (first_name, last_name, email)
  ),
  order_item (quantity),
  price_history (price, date_set)
FROM product
WHERE product_id = <productId>
LIMIT 1
```

### Seller aggregate query
```sql
SELECT product_id, order_item(quantity), review(rating)
FROM product
WHERE seller_id = <product.seller.seller_id>
```
- Used to calculate total products sold and average seller rating across ALL their products.

### Price History chart (`PriceHistory.jsx`)
```sql
SELECT price, date_set FROM price_history WHERE product_id = <productId>
```

### Monthly Stats (`ProductMonthlyStats.jsx`)
- Queries `order_item` joined with `order` to get monthly sales/revenue data.

---

## What It Can Do

1. **View product details** — name, description, price (formatted with `toLocaleString`), category badge.
2. **Image gallery** — click thumbnail to change main image.
3. **Quantity selector** — `QuantityButton` component, constrained by available stock.
4. **Buy Now** — navigates to `/product/confirm_order?productId=X&quantity=Y`.
5. **Add to Cart** — checks buyer not deleted, checks stock availability, inserts/updates `cartitem`.
6. **Edit Product** — `EditButton` links to `/product/edit?productId=X` (visible to seller).
7. **Price history line chart** — shows price changes over time.
8. **Monthly sales & revenue chart** — bar/line chart of sales by month.
9. **Reviews** — expandable list with star ratings, comments, timestamps, buyer name.
10. **Create Review** — form to submit a new review (rating + comment).
11. **Seller info panel** — store name, total products sold, average rating across all products.
12. **Deleted-entity guard** — if `product.is_deleted` or `product.seller.is_deleted`, shows "Product is disabled" screen.

---

## Typical UI

- Two-column layout: left side = image gallery + charts; right side = product info + actions.
- Large product image with thumbnail strip below.
- Category pill, product name (h1), star rating, review count, sold count, stock indicator.
- Large price in blue.
- Description paragraph.
- Quantity stepper + Buy Now + Add to Cart buttons.
- Seller info card with store stats.
- Full-width reviews section with show/hide toggle.
- Price history and monthly stats charts.
