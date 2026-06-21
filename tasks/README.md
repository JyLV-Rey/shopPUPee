# PolyPlaza Tasks

Pick one task, read the corresponding file, and implement the controller logic + Blade views for your assigned routes.

| # | Task | Assignee | Controller | Views to create |
|---|---|---|---|---|
| 01 | Auth (Login, Register, Seller App) | \_\_\_\_\_ | `AuthController` | `account/login`, `account/create`, `account/create_seller` |
| 02 | Products (View, Create, Edit) | \_\_\_\_\_ | `ProductController` | `product/view`, `product/create`, `product/edit` |
| 03 | Dashboards (Buyer & Seller) | \_\_\_\_\_ | `DashboardController` | `dashboard/buyer`, `dashboard/seller` |
| 04 | Shopping Cart | \_\_\_\_\_ | `CartController` | `cart/index` |
| 05 | Orders (Checkout, Receipt, History) | \_\_\_\_\_ | `OrderController` | `product/confirm_order`, `product/view_receipt`, `orders/index` |
| 06 | Profile & Address Management | \_\_\_\_\_ | `ProfileController` | `account/edit_buyer`, `account/edit_seller`, `address/edit`, `address/add` |
| 07 | Admin Panel | \_\_\_\_\_ | `AdminController` | `admin/buyers`, `admin/sellers`, `admin/orders`, `admin/applications`, `admin/products` |

---

## Reference files in `migration_info/`

Each task doc references specific pages in `migration_info/` — those describe how the original React app worked, what data it displayed, and what Supabase queries it ran. Use them as the source of truth for what each page should show.

## Common rules

1. All views extend `common.index`:
   ```blade
   @extends('common.index')
   @section('title', 'Page Title')
   @section('content')
     ... your HTML ...
   @endsection
   ```
2. Use DaisyUI classes (`card`, `btn`, `input input-bordered`, `table`, `badge`, etc.)
3. All controllers are at `app/Http/Controllers/` — open yours and fill in the methods
4. All models are at `app/Models/` with proper `#[Table]` attributes and relationships
5. Authentication: `Auth::user()` returns the logged-in `Buyer` instance
6. Soft deletes use `is_deleted` boolean (not Laravel's SoftDeletes trait)
7. Always eager-load relationships to avoid N+1 queries
8. MAKE YOUR OWN FOLDER!!!!!!

MAKE YOUR BRANCH YOUR SURNAME
