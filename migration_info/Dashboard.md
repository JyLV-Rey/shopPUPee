# Dashboard Pages

## BuyerDashboard (`/dashboard/buyer`)

**File:** `src/Pages/Dashboard/BuyerDashboard/BuyerDashboard.jsx`  
**Chart components folder:** `components/` (10 chart components)

### What It Does
A comprehensive analytics dashboard for a buyer — shows personal info, spending stats, and 10 data visualization charts.

### URL Interaction
**Query param:** `buyerId`
```
/dashboard/buyer?buyerId=<id>
```

### Supabase Queries
**Fetch buyer info:**
```sql
SELECT * FROM buyer WHERE buyer_id = <buyerId>
```

**Aggregate queries** (from `components/userAggregate.js`):
- `getTotalSpent(buyerId)` — sums `order_item.price * quantity` across all buyer's orders.
- `getTotalOrdersPlaced(buyerId)` — counts orders for this buyer.
- `getTotalCancelledOrders(buyerId)` — counts cancelled orders.
- `getTotalRefunds(buyerId)` — counts refunded orders.

**Charts each run their own Supabase queries** (pulled from `components/`):
1. `BuyerTopCategoryDoughnut` — most-bought product categories (pie/doughnut).
2. `BuyerSpendLineChart` — spending per month over time (line chart).
3. `BuyerSpendByCategoryBar` — total spend broken down by category (bar).
4. `BuyerPurchaseFrequencyBar` — how often they buy (bar).
5. `BuyerTopProductsBar` — most-purchased products (bar).
6. `BuyerReviewRatingBar` — rating distribution of reviews written (bar).
7. `BuyerPreferredSellersDoughnut` — preferred sellers by spend (doughnut).
8. `BuyerPaymentMethodPie` — payment method usage (pie).
9. `BuyerMostExpensiveBoughtItemsBar` — top items by price paid (bar).
10. `BuyerLeastExpensiveBoughtItemsBar` — cheapest items bought (bar).

### What It Can Do
1. Display buyer profile info (name, email, phone, join date, buyer ID).
2. Show total spent, total orders, cancelled count, refund count.
3. Render 10 different charts (wrapped in `ChartBox` or `PieChartBox`).
4. Links to View Orders (`/orders?buyerId=...`) and Edit Profile (`/edit/buyer?buyerId=...`).
5. Deleted-buyer guard.

### Typical UI
- Two-column header: left = personal info, right = stats (total spent, orders, etc.) + action buttons.
- Below: responsive flex-wrap grid of chart cards.
- Each chart card has a title bar + the chart component.

---

## SellerDashboard (`/dashboard/seller`)

**File:** `src/Pages/Dashboard/SellerDashbord/SellerDashboard.jsx`  
**Chart components folder:** `components/` (11 chart components)

### What It Does
Analytics dashboard for a seller — store info, sales stats, and 11 data visualization charts.

### URL Interaction
```
/dashboard/seller?sellerId=<id>
```

### Supabase Queries
**Fetch seller + buyer info:**
```sql
SELECT buyer_id, seller_name, is_deleted, applied_at,
  buyer (first_name, phone, is_deleted, last_name, email)
FROM seller
WHERE seller_id = <sellerId>
```

**Fetch all seller's order items:**
```sql
SELECT quantity, product (price, seller_id)
FROM order_item
```
- Filtered **client-side** to only items where `product.seller_id == sellerId`.
- Calculates `totalSold` (sum of quantities) and `totalEarned` (sum of `quantity * price`).

**Aggregate helpers** (from `components/sellerAggregate.js`):
- `getTotalCancelledBySeller(sellerId)` — cancelled order count.
- `getTotalRefundedBySeller(sellerId)` — refunded order count.
- `getAverageStoreRating(sellerId)` — average review rating across all seller's products.

**Charts** (each pulls from their own query):
1. `SellerTopProductsBar` — most-sold products (bar).
2. `SellerTopCategoriesBar` — most-sold categories (bar).
3. `SellerMonthlyEarningsLineChart` — earnings per month (line).
4. `SellerPurchaseFrequencyBar` — purchase frequency (bar).
5. `SellerRevenueRankingChart` — revenue rank vs other sellers (bar, highlighted).
6. `SellerSoldRankingChart` — quantity-sold rank vs other sellers (bar, highlighted).
7. `SellerTopReviewedProductsBar` — products with most reviews (bar).
8. `SellerEarningsByCategoryDoughnut` — earnings breakdown by category (doughnut).
9. `SellerTopBuyersBar` — top buyers by spend at this store (bar).
10. `SellerTopExpensiveProductsBar` — most expensive listed items (bar).
11. `SellerLeastExpensiveProductsBar` — cheapest listed items (bar).

### What It Can Do
1. Display seller profile info (store name, owner, email, phone, join date, store ID).
2. Show total earned, total sold, cancelled count, refunded count, average store rating (with star emojis).
3. Render 11 different charts.
4. Links to Create Product, View Products, Edit Profile.
5. Deleted-seller and deleted-buyer guards.

### Typical UI
- Two-column header: left = seller info, right = earnings/stats + action buttons.
- Star rating display with emoji stars.
- Responsive grid of chart cards below.

---

## Admin Dashboard (`/dashboard/admin/*`)

### AdminNavBar
**File:** `src/Pages/Dashboard/Admin/AdminNavBar.jsx`  
- Collapsible left sidebar (64px collapsed, 256px expanded).
- Links: Buyers List, Sellers List, Orders List, Products List, Applications.
- Active route highlighted in blue.

### BuyersView (`/dashboard/admin/buyer`)

**File:** `src/Pages/Dashboard/Admin/BuyersView/BuyersView.jsx`

Filters + lists all buyers. Sortable by any field. Filterable by `is_deleted`, `first_name`, `last_name`, `email`. Each row links to that buyer's dashboard. Admin can toggle `is_deleted` to enable/disable accounts.

**Queries:** `SELECT * FROM buyer` with optional `.eq('is_deleted', true/false)` and `.ilike` filters.

### SellersView (`/dashboard/admin/seller`)

**File:** `src/Pages/Dashboard/Admin/SellersView/SellersView.jsx`

Same pattern as BuyersView but for sellers. Shows seller ID, store name, owner name, application date. Links to seller dashboard. Toggle enable/disable.

**Queries:** `SELECT seller_id, seller_name, applied_at, is_deleted, buyer(first_name, last_name) FROM seller` with optional `.ilike('seller_name', ...)` and `.eq('is_deleted', ...)`.

### OrdersView (`/dashboard/admin/order`)

**File:** `src/Pages/Dashboard/Admin/OrdersView/OrdersView.jsx`

Lists all orders with buyer name filter. Links to order receipt. Toggle order `is_deleted`.

**Queries:** `SELECT *, buyer(first_name, last_name) FROM order` with title-case `.ilike` on buyer name and `.eq('is_deleted', ...)`.

### ProductsView (`/dashboard/admin/product`)

**File:** `src/Pages/Dashboard/Admin/ProductsView/ProductsView.jsx`

Lists all products with seller name, category, and product name filters. Links to product view. Toggle product `is_deleted`.

**Queries:** `SELECT product_id, name, price, quantity, is_deleted, description, category, created_at, seller(seller_name) FROM product` with `.ilike`/`.eq` filters.

### ApplicationsView (`/dashboard/admin/application`)

**File:** `src/Pages/Dashboard/Admin/ApplicationView/ApplicationsView.jsx`

Reviewed seller applications in 3 tabs: Pending, Approved, Rejected. Admin can approve (creates `seller` record) or reject. Shows applicant details, proposed store name, valid ID link, contact info, address.

**Tabbed queries:**
```sql
SELECT application_id, seller_name, application_date, valid_id_url, status, address_id,
  buyer:buyer_id (buyer_id, first_name, last_name, email, phone),
  address:address_id (address_id, street, city, postal_code)
FROM seller_application
ORDER BY application_date DESC
```

**Approve action:**
```sql
UPDATE seller_application SET status = 'Approved' WHERE application_id = <id>
INSERT INTO seller (buyer_id, address_id, seller_name, application_id) VALUES (...)
```
In case of error on seller insert, rolls back the status update.

**Reject action:**
```sql
UPDATE seller_application SET status = 'Rejected' WHERE application_id = <id>
```
