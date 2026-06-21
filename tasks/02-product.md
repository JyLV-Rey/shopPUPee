# Task: Product Pages — View, Create, Edit

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/ProductController.php`
**Views:** `resources/views/product/` (create this folder)
**Pages in `migration_info/`:** `Product.md`, `ViewProduct.md`

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/product/{product}/view` | `view(Product $product)` | `product.view` |
| GET | `/product/create` | `create()` | `product.create` |
| POST | `/product/create` | `store(Request $request)` | — (redirect) |
| GET | `/product/{product}/edit` | `edit(Product $product)` | `product.edit` |
| POST | `/product/{product}/edit` | `update(Request $request, Product $product)` | — (redirect) |

All `product/*` routes require authentication (`check.user` middleware).  
The `create` and `edit` routes should additionally check that the user is a seller.

---

## Route-model binding

`{product}` auto-resolves to a `Product` model by its `product_id`.  
If the product doesn't exist, Laravel returns a 404 automatically.  
In your controller method, type-hint it: `view(Product $product)` — the model is already loaded.

---

## Views to create

### `resources/views/product/view.blade.php`

**URL:** `/product/{product}/view`

**What it displays (per `migration_info/ViewProduct.md`):**
- Product name (large heading)
- Main product image (first from `$product->images`)
- All other product images as a thumbnail gallery
- Price (formatted: `₱X,XXX.XX`)
- Category as a badge
- Description (full text)
- Stock quantity — if 0, show "Out of Stock"
- Seller name — **clickable**, links to `/dashboard/{seller_id}/seller`
- Add to Cart button (if user is logged in, quantity selector + "Add to Cart")
- Customer Reviews section (list of reviews with buyer name, rating stars, comment, date)
- Review average + count
- If the product `is_deleted`, show a "This product is no longer available" message instead

**Eager load in controller:**
```php
$product->load(['images', 'seller', 'reviews.buyer']);
```

### `resources/views/product/create.blade.php`

**URL:** `/product/create`

**What it displays (per `migration_info/Product.md` → CreateProduct):**
- Two-column form layout
- **Left column:** product fields
  - Name (text input)
  - Description (textarea)
  - Price (number input, step 0.01)
  - Quantity (number input, integer)
  - Category (text input or dropdown — query existing categories)
- **Right column:** image URLs
  - Text input for image URL + "Add Image" button
  - List of added images with remove button per item
  - Live preview thumbnails of entered URLs
- "Create Product" submit button at bottom

**Form action:** `POST /product/create`
**On success:** redirect to `route('product.view', $product)`

**Authorization:** Check that `Auth::user()->seller` exists before showing the form. If not, redirect with error *"You need a seller account to create products."*

### `resources/views/product/edit.blade.php`

**URL:** `/product/{product}/edit`

**What it displays (per `migration_info/Product.md` → EditProduct):**
- Same form layout as CreateProduct, but **pre-filled** with existing product data
- Load existing images and show them with remove buttons
- Additional section:
  - **"Delete Product"** button — soft-deletes by setting `is_deleted = true`
  - Confirmation modal before deleting

**Form action:** `POST /product/{product}/edit`
**On success:** redirect to `route('product.view', $product)`

**Authorization:** Check that `$product->seller_id === Auth::user()->seller->seller_id`. If not, 403.

---

## Controller methods to implement

### `view(Product $product)`
- Load relations: `$product->load(['images', 'seller', 'reviews.buyer'])`
- Eager load the seller's products count or any other needed data
- Pass to view

### `create()`
- Check `Auth::user()->seller` exists
- Query distinct categories from products for the dropdown
- Return the create view

### `store(Request $request)`
- Validate: `name` (required), `description`, `price` (required, numeric), `quantity` (integer), `category`, `images` (array of URLs)
- Create: `Product::create([...])` with `seller_id` from `Auth::user()->seller->seller_id`
- Insert images: loop `$request->images` and `ProductImage::create([...])`
- Redirect to `route('product.view', $product)`

### `edit(Product $product)`
- Check seller ownership
- Load images
- Return the edit view with `$product`

### `update(Request $request, Product $product)`
- Validate same fields as store
- Update: `$product->update([...])`
- Handle image changes (add new, remove old)
- Redirect to `route('product.view', $product)`

---

## Models you'll use

| Model | Table | Key | Relations |
|---|---|---|---|
| `Product` | `product` | `product_id` | `images()`, `seller()`, `reviews()` |
| `ProductImage` | `product_image` | `image_id` | `product()` |
| `Seller` | `seller` | `seller_id` | `buyer()`, `products()` |
| `Review` | `review` | `review_id` | `product()`, `buyer()` |
| `Buyer` | `buyer` | `buyer_id` | `seller()` (HasOne) |

## Existing components you can use

- `<x-product_box :product="$product" />` — renders a product card
- `<x-pagination :paginator="$products" />` — pagination bar

## Layout

All views should `@extends('common.index')` and use `@section('title', '...')` / `@section('content')`.
