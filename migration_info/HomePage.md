# HomePage (`/`)

**File:** `src/Pages/HomePage/HomePage.jsx`

---

## What It Does

The landing page for PolyPlaza. Shows a hero section with a **search bar + filters**, a **scrollable category strip**, a **"Trending Now"** grid (4 cards), and a **"Featured Products"** grid (12 cards). If the user is logged in, it shows a personalized welcome banner with their name.

---

## URL Interaction

- **Static route** (`/`) — no URL params.
- The search form navigates to `/search` with query params:
  ```
  /search?searchTerm=...&searchCategory=...&isDescending=...&maxPrice=...&sortBy=...
  ```
- Category pills link to `/search?searchCategory=<category>`.
- "View All" links go to `/search?sortBy=orders&isDescending=Descending` and `/search`.

---

## Supabase Queries

### `fetchCategories()`
- Calls `getCategories()` from `SearchPage/Components/query.js`
- Queries `SELECT DISTINCT category FROM product` with null filter, returns sorted categories.

### `fetchTrendingProducts()`
```sql
SELECT product_id, name, price, category, is_deleted,
  product_image (image_url),
  seller (seller_name, is_deleted),
  review (rating),
  order_item (quantity)
FROM product
LIMIT 8
```
- Filters out deleted products/sellers **client-side**.
- Sorts by total `order_item.quantity` descending (most ordered = trending).
- Takes top 4 after sorting.

### `fetchFeaturedProducts()`
- Same query as trending but `LIMIT 12`, no sorting (recent products).

---

## What It Can Do

1. **Guest search** — anyone can search from the hero bar with category/price/sort filters.
2. **Category browsing** — click a category pill to jump to search pre-filtered.
3. **Trending discovery** — shows most-ordered products.
4. **Product cards** — each card (`ProductCard.jsx`) links to `/product/view?productId=...`, has "Add to Cart" and "Buy Now" buttons.

---

## Typical UI

- Full-viewport hero with a background splash image (`/splash-photo.png`) and dark overlay.
- Centered logo + "PolyPlaza" heading.
- Greeting banner (only when logged in) with user's first name.
- Large search input + 4 filter dropdowns in a row.
- Horizontal-scrolling category strip with left/right arrow buttons.
- Three sections: **Trending Now** (4-column grid), **Featured Products** (4-column grid).
- NavBar at top, Footer at bottom.
