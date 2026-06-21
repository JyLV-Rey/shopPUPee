# Task: Order Flow — Checkout, Receipt, Order History

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/OrderController.php`
**Views:** `resources/views/` (see each section for folders)
**Pages in `migration_info/`:** `ConfirmOrderPage.md`, `ViewReceipt.md`, `ViewOrders.md`

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/orders` | `orders(Request $request)` | `orders.index` |
| GET | `/product/confirm_order` | `confirmOrder(Request $request)` | `product.confirm_order` |
| GET | `/product/view_receipt` | `viewReceipt(Request $request)` | `product.view_receipt` |

All require authentication (`check.user` middleware).

---

## Views to create

### `resources/views/orders/index.blade.php`

**URL:** `/orders`

**What it displays (per `migration_info/ViewOrders.md`):**

A list of all orders placed by the logged-in buyer.

- Page title: "My Orders"
- If no orders: empty state with link to browse products

**Order list:**
Each order shown as a card with:
- Order ID (`#order_id`)
- Status badge (color-coded: Pending = warning, Processing = info, Shipped = primary, Delivered = success, Cancelled = error, Refunded = secondary)
- Order date (formatted)
- Total price (sum of all items)
- Items preview (first 3 item names + "...and N more")
- "View Receipt" button → `/product/view_receipt?orderId=X`

**Filters/sort (optional but nice):**
- Sort by date (newest/oldest)
- Filter by status

**Controller query:**
```php
$orders = Order::with(['items.product'])
    ->where('buyer_id', Auth::user()->buyer_id)
    ->orderBy('ordered_at', 'desc')
    ->paginate(10);
```

---

### `resources/views/product/confirm_order.blade.php`

**URL:** `/product/confirm_order?cartItems=1,2,3`

**What it displays (per `migration_info/ConfirmOrderPage.md`):**

The checkout/order confirmation page. Shows what the user is about to buy and asks them to confirm.

**Query param:** `cartItems` — comma-separated `cart_item_id` values

**Page layout:**
- Title: "Confirm Order"
- If `cartItems` is empty: redirect to cart with error

**Order summary:**
- List of items being purchased (from the cart items):
  - Product image (thumbnail)
  - Product name
  - Unit price
  - Quantity
  - Subtotal
- **Total:** sum of all subtotals

**Delivery address section:**
- Show the buyer's saved addresses
- Radio buttons to select which address to deliver to
- If no addresses saved, show link to `/add/address`

**Confirm button:**
- "Place Order" button
- On click, creates the order + order items + payment record + delivery record, then:
  - Deletes the cart items
  - Redirects to `/product/view_receipt?orderId=X&justOrdered=true`

**Controller logic for confirming:**
```php
// 1. Parse cart item IDs from query string
$cartItemIds = explode(',', $request->query('cartItems'));
$cartItems = CartItem::with('product')->whereIn('cart_item_id', $cartItemIds)
    ->where('buyer_id', Auth::user()->buyer_id)->get();

// 2. Calculate total
$total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

// For GET: just show the summary
// For POST (place order): create everything
```

**For the POST (place order):**
```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($cartItems, $request) {
    $order = Order::create([
        'buyer_id' => Auth::user()->buyer_id,
        'status' => 'Pending',
        'ordered_at' => now(),
    ]);

    foreach ($cartItems as $item) {
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ]);
    }

    Payment::create([
        'order_id' => $order->order_id,
        'payment_method' => $request->payment_method ?? 'Cash on Delivery',
        'payment_status' => 'Pending',
    ]);

    Delivery::create([
        'order_id' => $order->order_id,
        'delivery_status' => 'Preparing',
        'buyer_address_id' => $request->address_id,
    ]);

    // Clear cart items
    CartItem::whereIn('cart_item_id', $cartItems->pluck('cart_item_id'))->delete();
});
```

**You'll need to add a POST route:**
```php
Route::post('/product/confirm_order', [OrderController::class, 'placeOrder'])->name('order.place');
```

---

### `resources/views/product/view_receipt.blade.php`

**URL:** `/product/view_receipt?orderId=X`

**What it displays (per `migration_info/ViewReceipt.md`):**

A receipt page shown after an order is placed, or viewable later from order history.

**Query params:**
- `orderId` — required, the order to show
- `?justOrdered=true` — show a success banner: *"Order placed successfully!"*

**What to display:**
- Success banner (if `justOrdered`)
- Receipt header: "Receipt" + Order ID + Date
- Seller info per-item group
- Items table: Product name, Price, Quantity, Subtotal
- **Total** at bottom
- Delivery address
- Payment method + status
- Delivery status + tracking number
- "Back to Orders" button → `/orders`

**Controller query:**
```php
$order = Order::with(['items.product.seller', 'delivery', 'payment', 'buyer'])
    ->where('order_id', $request->query('orderId'))
    ->firstOrFail();
```

---

## Models you'll use

| Model | Key Relations |
|---|---|
| `Order` | `buyer()`, `items()` (OrderItem), `payment()`, `delivery()` |
| `OrderItem` | `order()`, `product()` |
| `Payment` | `order()` |
| `Delivery` | `order()`, `address()` |
| `CartItem` | `buyer()`, `product()` |
| `Product` | `images()`, `seller()` |
| `Address` | `buyer()` |

## Layout

`@extends('common.index')` with `@section('title', ...)` / `@section('content')`.

Use DaisyUI components: `card`, `table`, `badge`, `btn`, `steps` (for delivery tracking), `alert` (for success banners).
