# Task: Shopping Cart

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/CartController.php`
**Views:** `resources/views/cart/` (create this folder)
**Pages in `migration_info/`:** `CartPage.md`

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/cart` | `index()` | `cart.index` |

Requires authentication (`check.user` middleware).

---

## Views to create

### `resources/views/cart/index.blade.php`

**URL:** `/cart`

**What it displays (per `migration_info/CartPage.md`):**

The cart page shows all items the current user has in their cart, grouped by seller/store.

**Page layout:**
- Title: "Shopping Cart"
- If cart is empty: show a friendly empty-state message with a link to browse products

**Cart items list (grouped by seller):**
For each seller the buyer has items from:
- Seller name heading
- Table of items from that seller with columns:
  - Product image (thumbnail)
  - Product name (clickable → `/product/{id}/view`)
  - Unit price (formatted)
  - Quantity selector (+ / - buttons with number display)
  - Subtotal (price × quantity)
  - Remove button (trash icon)

**Cart summary sidebar or bottom bar:**
- Total items count
- Total price (sum of all subtotals)
- "Proceed to Checkout" button → `/product/confirm_order?cartItems=id1,id2,id3`

**Per-item controls:**
- Quantity can be increased/decreased in the cart (update via POST or AJAX)
- Items can be removed entirely

---

## Controller methods to implement

### `index()`
- Get the authenticated user: `$buyer = Auth::user()`
- Query cart items for this buyer with product and seller data:
```php
$cartItems = CartItem::with(['product.images', 'product.seller'])
    ->where('buyer_id', $buyer->buyer_id)
    ->get();
```
- Group by seller:
```php
$grouped = $cartItems->groupBy(fn ($item) => $item->product->seller->seller_name);
```
- Pass `$cartItems` and `$grouped` to the view
- Compute totals: `$total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity)`

### Additional methods you'll need to add:

**`update(Request $request, CartItem $cartItem)`** — update quantity
- Validate quantity > 0
- `$cartItem->update(['quantity' => $request->quantity])`
- Return redirect back with success

**`destroy(CartItem $cartItem)`** — remove item
- `$cartItem->delete()`
- Return redirect back with success

**Add these routes to `routes/web.php`:**
```php
Route::post('/cart/{cartItem}/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
```

---

## Models you'll use

| Model | Key Relations |
|---|---|
| `CartItem` | `buyer()`, `product()` |
| `Product` | `images()`, `seller()` |
| `Seller` | `buyer()` |

## Existing components you can use

- `<x-product_box :product="$product" />` — for browse-more suggestions at the bottom

## Layout

`@extends('common.index')` with `@section('title', 'Cart')` / `@section('content')`.

Use DaisyUI components: `table`, `card`, `btn`, `btn-ghost`, `badge`, `join` (for quantity controls).
