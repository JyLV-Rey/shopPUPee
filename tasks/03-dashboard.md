# Task: Dashboards — Buyer & Seller

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/DashboardController.php`
**Views:** `resources/views/dashboard/` (create this folder)
**Pages in `migration_info/`:** `Dashboard.md`

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/dashboard/{buyer}/buyer` | `buyer(Buyer $buyer)` | `dashboard.buyer` |
| GET | `/dashboard/{seller}/seller` | `seller(Seller $seller)` | `dashboard.seller` |

Both require authentication (`check.user` middleware).

---

## Route-model binding

`{buyer}` auto-resolves to a `Buyer` model.  
`{seller}` auto-resolves to a `Seller` model.

---

## Views to create

### `resources/views/dashboard/buyer.blade.php`

**URL:** `/dashboard/{buyer}/buyer`

**What it displays (per `migration_info/Dashboard.md` → BuyerDashboard):**

**Header section (two columns):**
- **Left:** Buyer info card
  - Full name (`$buyer->first_name . ' ' . $buyer->last_name`)
  - Email
  - Phone
  - Member since (formatted `created_at` date)
  - Buyer ID (`buyer_id`)
- **Right:** Stats card
  - Total Spent (sum of all completed orders)
  - Total Orders Placed (count)
  - Total Cancelled Orders (count)
  - Total Refunds (count)
  - Action buttons: "View Orders" → `/orders`, "Edit Profile" → `/edit/buyer` (use query params for IDs)

**Charts section (10 charts — you can start with simpler versions):**
Refer to `migration_info/Dashboard.md` for the full chart descriptions. Each chart is one card in a responsive grid.

1. **Top Categories Doughnut** — most-purchased product categories (group by `product.category` in `order_item` → `product`)
2. **Spending Over Time Line** — total spent per month (group by month from `order.ordered_at`)
3. **Spend by Category Bar** — total spend per category
4. **Purchase Frequency Bar** — how many orders per month
5. **Top Products Bar** — most-purchased products by quantity
6. **Review Ratings Bar** — distribution of review ratings (1-5 stars)
7. **Preferred Sellers Doughnut** — spend grouped by seller name
8. **Payment Methods Pie** — order count by payment method
9. **Most Expensive Items Bar** — top 5 items by price paid
10. **Least Expensive Items Bar** — bottom 5 items by price paid

> **Tip:** You can use a simple HTML/CSS bar chart or include a charting library like Chart.js. For a first pass, even a table of values is fine — make it visual later.

### `resources/views/dashboard/seller.blade.php`

**URL:** `/dashboard/{seller}/seller`

**What it displays (per `migration_info/Dashboard.md` → SellerDashboard):**

**Header section:**
- Store name (`$seller->seller_name`)
- Seller since (formatted `applied_at`)
- Total products listed (count of `$seller->products`)
- Total items sold (sum of `order_item.quantity` across all seller's products)
- Total revenue (sum of `order_item.price * quantity`)
- Action buttons: "Create Product" → `/product/create`, "Edit Store" → `/edit/seller`

**Charts section:**
The Seller Dashboard has its own set of charts (see `migration_info/Dashboard.md` for the full list). Key ones:
1. Revenue Over Time (line chart)
2. Top Selling Products (bar chart)
3. Category Breakdown (pie/doughnut)
4. Order Status Distribution
5. Monthly Sales Comparison
6. Low Stock Alerts (table of products with quantity < 5)

**Products list:**
- Table of all seller's products with columns: Name, Price, Stock, Status (active/deleted), Actions (Edit → `/product/{id}/edit`, View → `/product/{id}/view`)

---

## Controller methods to implement

### `buyer(Buyer $buyer)`
- Eager load: `$buyer->load(['orders.items.product', 'reviews'])`
- Compute aggregate stats (total spent, order counts) from the loaded relations
- Pass `$buyer` to the view
- The view uses `$buyer` directly (no extra queries needed if eager-loaded)

### `seller(Seller $seller)`
- Eager load: `$seller->load(['buyer', 'products.images'])`
- Also load `products.orderItems` for sales data
- Compute stats: total products, total sold, total revenue
- Pass `$seller` and the related `$buyer` to the view

---

## Models you'll use

| Model | Key Relations |
|---|---|
| `Buyer` | `orders()`, `reviews()`, `seller()` (HasOne) |
| `Seller` | `buyer()`, `products()` |
| `Order` | `buyer()`, `items()` (OrderItem) |
| `OrderItem` | `order()`, `product()` |
| `Product` | `seller()`, `images()`, `orderItems()` |

## Example aggregate query (for Buyer total spent)

```php
use Illuminate\Support\Facades\DB;

$totalSpent = DB::table('order_item')
    ->join('order', 'order_item.order_id', '=', 'order.order_id')
    ->join('product', 'order_item.product_id', '=', 'product.product_id')
    ->where('order.buyer_id', $buyer->buyer_id)
    ->where('order.is_deleted', false)
    ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
    ->sum(DB::raw('product.price * order_item.quantity'));
```

## Layout

All views should `@extends('common.index')` and use `@section('title', '...')` / `@section('content')`.

Use DaisyUI components: `card`, `stats`, `table`, `badge`, `btn`, `avatar`.
