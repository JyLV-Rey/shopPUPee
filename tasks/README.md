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

---

## Using Chart.js for dashboards

Chart.js is installed and wired into Vite. Usage is dead simple:

### 1. Pass chart data from your controller

In your controller method, build arrays for labels and datasets:

```php
public function buyer(Buyer $buyer)
{
    $categoryData = DB::table('order_item')
        ->join('order', 'order_item.order_id', '=', 'order.order_id')
        ->join('product', 'order_item.product_id', '=', 'product.product_id')
        ->where('order.buyer_id', $buyer->buyer_id)
        ->select('product.category', DB::raw('SUM(order_item.quantity) as total'))
        ->groupBy('product.category')
        ->get();

    return view('dashboard.buyer', [
        'buyer' => $buyer,
        'chartCategories' => [
            'labels' => $categoryData->pluck('category'),
            'values' => $categoryData->pluck('total'),
        ],
    ]);
}
```

### 2. Load the charts JS on your page

```blade
@section('content')
    @vite('resources/js/charts.js')
    ...
@endsection
```

### 3. Use the `<x-chart>` component

```blade
<x-chart
    id="categoryChart"
    type="doughnut"
    :labels="$chartCategories['labels']"
    :datasets="[[
        'data' => $chartCategories['values'],
        'backgroundColor' => ['#4f46e5', '#a855f7', '#06b6d4', '#22c55e', '#f59e0b'],
    ]]"
    title="Top Categories"
/>
```

### Supported chart types

| `type` | Renders |
|---|---|
| `bar` | Vertical bar chart |
| `line` | Line chart |
| `doughnut` | Doughnut/pie chart |
| `pie` | Pie chart |
| `radar` | Radar chart |
| `polarArea` | Polar area chart |

### Custom dataset options

The component accepts full Chart.js dataset objects, so you can pass extra options:

```blade
:datasets="[[
    'label' => 'Sales',
    'data' => [12, 19, 3, 5],
    'backgroundColor' => '#4f46e5',
    'borderColor' => '#312e81',
    'borderWidth' => 2,
    'tension' => 0.3,        // for line charts — smooth curves
    'fill' => true,           // fill area under line
]]"
```

---

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
