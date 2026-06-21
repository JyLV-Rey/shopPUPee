# Task: Admin Panel

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/AdminController.php`
**Views:** `resources/views/admin/` (create this folder)
**Pages in `migration_info/`:** `Dashboard.md` (Admin section)

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/dashboard/admin/buyer` | `buyers()` | `admin.buyers` |
| GET | `/dashboard/admin/seller` | `sellers()` | `admin.sellers` |
| GET | `/dashboard/admin/order` | `orders()` | `admin.orders` |
| GET | `/dashboard/admin/application` | `applications()` | `admin.applications` |
| GET | `/dashboard/admin/product` | `products()` | `admin.products` |

All require `check.admin` middleware — only users with `is_admin = true` on the `buyer` table can access these.

---

## Views to create

### `resources/views/admin/buyers.blade.php`

**URL:** `/dashboard/admin/buyer`

**What it displays (per `migration_info/Dashboard.md` → BuyersView):**

A management page for viewing all buyers, with the ability to disable (soft-delete) accounts.

**Buyers table:**
- Columns: Buyer ID, Name, Email, Phone, Joined Date, Status (Active/Deleted), Actions
- Status column: show "Active" (green badge) or "Deleted" (red badge) based on `is_deleted`
- Actions column: "Disable" button (sets `is_deleted = true`) or "Restore" button (sets `is_deleted = false`)
- Search bar to filter by name or email

**Controller query:**
```php
$buyers = Buyer::withCount('orders')
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

---

### `resources/views/admin/sellers.blade.php`

**URL:** `/dashboard/admin/seller`

**What it displays (per `migration_info/Dashboard.md` → SellersView):**

A management page for viewing all sellers, with the ability to disable seller accounts.

**Sellers table:**
- Columns: Seller ID, Store Name, Owner Name (buyer), Email, Products Count, Status, Actions
- Actions: "Disable" / "Restore"

**Controller query:**
```php
$sellers = Seller::with('buyer')->withCount('products')
    ->orderBy('seller_id', 'desc')
    ->paginate(20);
```

---

### `resources/views/admin/orders.blade.php`

**URL:** `/dashboard/admin/order`

**What it displays (per `migration_info/Dashboard.md` → OrdersView):**

A management page for viewing all orders, with the ability to cancel or mark orders.

**Orders table:**
- Columns: Order ID, Buyer, Date, Items Count, Total, Status, Actions
- Status: color-coded badges (Pending=yellow, Processing=blue, Shipped=purple, Delivered=green, Cancelled=red, Refunded=gray)
- Actions: "Cancel Order", "Mark as Delivered", "Process Refund"
- Click row to expand / view items

**Controller query:**
```php
$orders = Order::with(['buyer', 'items.product'])
    ->orderBy('ordered_at', 'desc')
    ->paginate(20);
```

---

### `resources/views/admin/applications.blade.php`

**URL:** `/dashboard/admin/application`

**What it displays (per `migration_info/Dashboard.md` → ApplicationsView):**

A management page for reviewing seller applications. This is where admins approve or reject requests to become sellers.

**Applications table:**
- Columns: Application ID, Applicant Name, Email, Store Name, Date Applied, Status, Address, Valid ID, Actions
- Status: Pending (warning), Approved (success), Rejected (error)
- Actions: **"Approve"** and **"Reject"** buttons for pending applications
- When approved: create a `seller` record linked to the buyer and update application status
- When rejected: just update application status

**Controller query:**
```php
$applications = SellerApplication::with('buyer', 'address')
    ->orderBy('application_date', 'desc')
    ->paginate(20);
```

**Approve logic (add a POST route for this):**
```php
// When approving:
DB::transaction(function () use ($application) {
    $application->update(['status' => 'Approved']);

    Seller::create([
        'buyer_id' => $application->buyer_id,
        'seller_name' => $application->seller_name,
        'address_id' => $application->address_id,
        'application_id' => $application->application_id,
    ]);
});
```

**Reject logic:**
```php
$application->update(['status' => 'Rejected']);
```

**You'll need to add POST routes:**
```php
Route::post('/application/{application}/approve', [AdminController::class, 'approveApplication'])->name('admin.application.approve');
Route::post('/application/{application}/reject', [AdminController::class, 'rejectApplication'])->name('admin.application.reject');
```

---

### `resources/views/admin/products.blade.php`

**URL:** `/dashboard/admin/product`

**What it displays (per `migration_info/Dashboard.md` → ProductsView):**

A management page for viewing all products, with the ability to disable listings.

**Products table:**
- Columns: Product ID, Name, Price, Category, Seller, Stock, Status, Actions
- Status: Active (green) / Deleted (red) based on `is_deleted`
- Actions: "Disable" / "Restore"

**Controller query:**
```php
$products = Product::with('seller')
    ->orderBy('product_id', 'desc')
    ->paginate(20);
```

---

## Controller methods to implement

Each method follows the same pattern — query the relevant model, paginate, pass to view.

For the approve/reject actions on applications:

```php
public function approveApplication(SellerApplication $application)
{
    // TODO: implement approve logic
}

public function rejectApplication(SellerApplication $application)
{
    // TODO: implement reject logic
}
```

---

## Models you'll use

| Model | Table | Key Relations |
|---|---|---|
| `Buyer` | `buyer` | `orders()`, `seller()` |
| `Seller` | `seller` | `buyer()`, `products()` |
| `Order` | `order` | `buyer()`, `items()` |
| `OrderItem` | `order_item` | `order()`, `product()` |
| `Product` | `product` | `seller()` |
| `SellerApplication` | `seller_application` | `buyer()`, `address()` |

## Layout

`@extends('common.index')` with `@section('title', 'Admin - ...')` / `@section('content')`.

Use DaisyUI components: `table`, `badge`, `btn`, `btn-error`, `btn-success`, `modal` (for confirmations), `input input-bordered` (for search).
