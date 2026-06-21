# SearchPage (`/search`)

**Files:**
- `src/Pages/SearchPage/SearchPage.jsx` — layout shell
- `src/Pages/SearchPage/Components/SearchBar.jsx` — filter bar component
- `src/Pages/SearchPage/Components/SearchItems.jsx` — reads query params, delegates to ProductList
- `src/Pages/SearchPage/Components/ProductList.jsx` — main query + rendering
- `src/Pages/SearchPage/Components/query.js` — shared category fetch
- `src/Pages/SearchPage/Components/LoadingScreen.jsx` — spinner

---

## What It Does

Displays search results from the `active_products` view (a Supabase view that filters out deleted products). Users filter by **search term**, **category**, **sort** (reviews / price / orders), **order direction**, **max price**, and **store name**.

When no results match, it shows an "Explore Other Products" section with up to 12 random products.

---

## URL Interaction

**Query-param-driven** — all state is in the URL:
```
/search
  ?searchTerm=<string>
  &searchCategory=<string>
  &isDescending=Ascending|Descending
  &maxPrice=<number>
  &sortBy=reviews|price|orders
  &searchStore=<store name>
```

- `SearchBar` reads from `useLocation().search` on mount to pre-fill inputs.
- Every search submit **replaces** the URL (using `navigate`).
- `SearchItems` reads params via `useSearchParams()` and passes them to `ProductList`.
- Product cards link to `/product/view?productId=...`.

---

## Supabase Queries

### Main query (`ProductList.fetchItems`)

```sql
SELECT product_id, name, category, quantity, price,
  product_image (image_url),
  seller (seller_name),
  review (rating),
  order_item (quantity)
FROM active_products
WHERE is_deleted = false
  [AND name ILIKE '%<searchTerm>%']
  [AND category = '<searchCategory>']
  [AND price <= <maxPrice>]
  [AND seller_id IN (<matching seller ids from seller ILIKE query>)]
ORDER BY price [ASC|DESC]
LIMIT 500
```

- `active_products` is a Supabase view (pre-filtered, non-deleted).
- **Reviews sort** — done client-side: computes average rating per product, sorts in JS.
- **Orders sort** — done client-side: sums `order_item.quantity` per product, sorts in JS.
- **Store search** — first queries `seller` table with `ILiKE seller_name`, then filters `product` by matching `seller_id`s.

### Explore Products (fallback when 0 results)
```sql
SELECT ... FROM product WHERE is_deleted = false
  AND seller.is_deleted = false
LIMIT 12
```
- Client-side filters out deleted sellers.

### Categories (`query.js`)
```sql
SELECT category FROM product
WHERE category IS NOT NULL
ORDER BY category ASC
LIMIT 1000
```
- Deduplicated client-side via `new Set()`.

---

## What It Can Do

1. **Full-text search** — case-insensitive product name matching (`ILIKE`).
2. **Category filter** — exact match on `category` field.
3. **Price cap** — numeric `<=` filter.
4. **Sort by** — price (DB-level), reviews average (client-side), total orders (client-side).
5. **Ascending/descending** toggle for all sort modes.
6. **Store search** — searches seller names separately, then filters products.
7. **Add to cart** inline — checks buyer not deleted, checks stock, inserts/updates `cartitem`.
8. **Buy now** — links directly to `/product/confirm_order?productId=X&quantity=1`.
9. **Fallback explore** — when 0 results, shows random products.

---

## Typical UI

- Sticky filter bar below NavBar (`fixed top-20 z-40`) with the search input, a "Filters" toggle button, and a "Search" button.
- Collapsible filter panel with 4 dropdowns + 1 text input (store search).
- Products displayed as a responsive card grid (wrap, flex).
- Each card: image, name, star rating, sold count, stock count, price, seller name, "Add to Cart" + "Buy Now" buttons.
- Loading skeleton shown during fetch.
- Empty state with search icon + "Explore Other Products" fallback grid.
