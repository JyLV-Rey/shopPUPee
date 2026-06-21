# Product Pages

## CreateProduct (`/product/create`)

**File:** `src/Pages/Product/CreateProduct.jsx`

### What It Does
Form for sellers to create a new product listing. Collects name, description, price, quantity, category, and one or more image URLs.

### URL Interaction
**Query param:** `sellerId`
```
/product/create?sellerId=<id>
```
On success navigates to `/product/view?productId=<new_id>`.

### Supabase Queries
```sql
INSERT INTO product (name, description, price, category, seller_id, quantity)
VALUES (<...>)
RETURNING product_id
```

Then for each image URL:
```sql
INSERT INTO product_image (product_id, image_url) VALUES (<id>, '<url>')
```

### What It Can Do
1. Create product with all required fields.
2. Add/remove multiple image URLs (dynamic list).
3. Preview images from entered URLs.
4. Validates price and quantity are whole numbers via regex.
5. Navigates to the new product page on success.

### Typical UI
- Two-column form layout: left = product info fields, right = image URLs with live previews.
- "Add Image" button + remove buttons per image.
- "Create Product" submit button.
- Status text at bottom (success/error).

---

## EditProduct (`/product/edit`)

**File:** `src/Pages/Product/EditProduct.jsx`

### What It Does
Lets the seller edit an existing product's fields and images, or soft-delete the product entirely.

### URL Interaction
```
/product/edit?productId=<id>
```
On save navigates to `/product/view?productId=<id>`. On delete navigates to a dashboard page.

### Supabase Queries

**Fetch product (on mount):**
```sql
SELECT name, description, price, category, quantity
FROM product WHERE product_id = <productId>
```

**Fetch images:**
```sql
SELECT image_url FROM product_image WHERE product_id = <productId>
```

**Update product:**
```sql
UPDATE product SET name, description, price, category, quantity
WHERE product_id = <productId>
```

**Replace images (delete all + re-insert):**
```sql
DELETE FROM product_image WHERE product_id = <productId>
INSERT INTO product_image (product_id, image_url) VALUES (...)
```

**Soft delete:**
```sql
UPDATE product SET is_deleted = true WHERE product_id = <productId>
```

### What It Can Do
1. Pre-fill form with existing product data.
2. Edit name, description, price, category, quantity.
3. Add/remove/reorder image URLs with previews.
4. Save changes (replaces image set entirely).
5. Delete product (soft delete via `is_deleted = true`).
6. Validates price and quantity are whole numbers.

### Typical UI
- Same two-column layout as CreateProduct.
- Additional "Delete Product" button (red) next to "Update Product" button.
- Status text at bottom.
