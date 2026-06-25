
<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/badge/ShopPUPee-Marketplace-8b5cf6?style=for-the-badge&logo=laravel&logoColor=white">
    <img alt="ShopPUPee" src="https://img.shields.io/badge/ShopPUPee-Marketplace-6366f1?style=for-the-badge&logo=laravel&logoColor=white">
  </picture>
</p>

<p align="center">
  <strong>A multi-vendor marketplace built on Laravel, Tailwind CSS, DaisyUI, and Supabase.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777bb3?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-13-fb503b?logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Tailwind-4-38bdf8?logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/DaisyUI-5-5a0ef8?logo=daisyui&logoColor=white" alt="DaisyUI">
  <img src="https://img.shields.io/badge/Vite-8-646cff?logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Supabase-3ecf8e?logo=supabase&logoColor=white" alt="Supabase">
</p>

---

## Features

- **Buyer accounts** — register, login, manage profile and addresses
- **Seller applications** — buyers apply to become sellers; admin approves/rejects with DB transaction
- **Product management** — sellers create, edit, update products with image gallery + reviews
- **Shopping cart** — add items, local quantity changes (no DB hits), checkbox selection
- **Order placement** — confirm order with address, payment method, courier selection; stock decremented
- **Order tracking** — buyers view order history; sellers track outgoing orders + update delivery status
- **Admin panel** — sidebar navigation to manage buyers, sellers, products, orders, and applications
- **Soft-delete system** — disable/restore buyers, sellers, and products via admin panel
- **Role-based access** — guest, buyer, seller, and admin tiers with middleware guards
- **Product analytics** — price history, units sold, and earnings charts per product
- **Dashboard charts** — 10 buyer analytics charts + 10 seller analytics charts (Chart.js)
- **Review system** — star rating + comment with upsert logic; reviewer links to buyer dashboard
- **Theme toggle** — light (garden) / dark (dim) with localStorage persistence
- **Bouncing ball canvas** — decorative animated background that adapts to theme

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.3+, Laravel 13 |
| **Frontend** | Blade templates, Tailwind CSS 4, DaisyUI 5 |
| **Charts** | Chart.js 4 auto-rendered via `data-chart` attributes |
| **Build** | Vite 8 + `laravel-vite-plugin` |
| **Database** | Supabase (PostgreSQL 15+) with custom enums |
| **Auth** | Session-based against `buyer` table |

---

## Route Map (46 routes)

### Public (no auth required)

| Method | URL | Purpose |
|---|---|---|
| GET | `/` | Home page — hero, search, trending & featured products |
| GET | `/search` | Search with filters, sorting, pagination |
| GET | `/product/{id}/view` | Product detail with gallery, reviews, charts |

### Guest-only (not logged in)

| Method | URL | Purpose |
|---|---|---|
| GET/POST | `/account/login` | Buyer login form + submit |
| GET/POST | `/account/create` | Buyer registration |
| GET/POST | `/account/create/seller` | Seller application form |

### Authenticated (buyer)

| Method | URL | Purpose |
|---|---|---|
| GET/POST | `/cart` / `/cart/add` | View cart / add item |
| POST/DELETE | `/cart/{id}/update` / `/cart/{id}` | Update qty / remove item |
| GET/POST | `/product/confirm_order` | Checkout page / place order |
| GET | `/product/view_receipt` | Order receipt |
| GET | `/orders` | Order history with status filter |
| GET/POST | `/product/{id}/review` | Submit / update review |
| GET | `/dashboard/{buyerId}/buyer` | Buyer dashboard (10 charts) |
| GET/POST | `/edit/buyer` | Edit profile |
| GET/POST | `/edit/address` | Edit address |
| GET/POST | `/add/address` | Add address |

### Authenticated (seller)

| Method | URL | Purpose |
|---|---|---|
| GET/POST | `/product/create` | Create product |
| GET/PUT | `/product/{id}/edit` | Edit product |
| GET | `/dashboard/{sellerId}/seller` | Seller dashboard (10 charts) |
| GET | `/dashboard/{sellerId}/orders` | Outgoing orders with delivery updates |
| POST | `/delivery/{deliveryId}/status` | Update delivery status |
| GET/POST | `/edit/seller` | Edit store profile |

### Admin only

| Method | URL | Purpose |
|---|---|---|
| GET | `/dashboard/admin/buyer` | Manage buyers (disable/restore) |
| GET | `/dashboard/admin/seller` | Manage sellers (disable/restore) |
| GET | `/dashboard/admin/order` | Manage orders (update status) |
| GET | `/dashboard/admin/application` | Approve/reject seller applications |
| GET | `/dashboard/admin/product` | Manage products (disable/restore) |
| POST | `/dashboard/admin/*/toggle` | Toggle disable/restore on entities |
| POST | `/dashboard/admin/application/*/approve` | Approve application (creates seller) |
| POST | `/dashboard/admin/application/*/reject` | Reject application |

### Middleware

| Alias | Class | Behavior |
|---|---|---|
| `check.user` | `CheckUser` | Redirects to login if unauthenticated; checks `is_deleted` |
| `check.admin` | `CheckAdmin` | Requires `is_admin = true`; 403 otherwise |

---

## Database Schema

All 15 tables in Supabase with Eloquent models in `app/Models/`.

### Entity Relationships

```
buyer ──┬── seller (HasOne)
        ├── address (HasMany)
        ├── cartitem (HasMany)
        ├── review (HasMany)
        ├── seller_application (HasMany)
        └── order (HasMany)

seller ──┬── product (HasMany)
         └── seller_application (BelongsTo)

product ──┬── product_image (HasMany)
          ├── price_history (HasMany)
          ├── review (HasMany)
          ├── cartitem (HasMany)
          └── order_item (HasMany)

order ──┬── order_item (HasMany)
        ├── payment (HasOne)
        ├── delivery (HasOne)
        ├── cancel (HasOne)
        └── refund (HasOne)
```

### PostgreSQL Enums

| Enum | Valid Values |
|---|---|
| `order_status` | `Pending`, `Paid`, `Shipped`, `Cancelled`, `Refunded` |
| `delivery_status` | `Preparing`, `In Transit`, `Delivered`, `Failed`, `Returned`, `Cancelled` |
| `payment_method` | `COD`, `Card`, `Wallet`, `UPI` |
| `payment_status` | `Pending`, `Success`, `Failed`, `Refunded` |
| `seller_status` | `Pending`, `Approved`, `Rejected` |

### Key Design Decisions

- **Auth against `buyer`** — no separate `users` table; `Buyer extends Authenticatable`
- **Soft deletes** — `is_deleted` boolean column (not Laravel's `SoftDeletes`)
- **`SoftDeletesFlag` trait** — `scopeActive()` / `scopeDeleted()` for PostgreSQL-compatible boolean queries
- **Route-model binding** — `{buyer}`, `{seller}`, `{product}` auto-resolve with `whereNumber` constraints

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 9 controllers
│   │   ├── Middleware/          # CheckUser, CheckAdmin
│   ├── Models/
│   │   ├── Buyer.php           # Authenticatable, dashboard aggregation methods
│   │   ├── Seller.php          # Seller dashboard + order tracking methods
│   │   ├── Product.php         # CRUD, relationships, scopes
│   │   ├── Order.php           # Order lifecycle
│   │   ├── ...
│   │   └── Concerns/
│   │       └── SoftDeletesFlag.php  # Reusable soft-delete trait
├── bootstrap/app.php           # Middleware registration
├── config/
├── database/
│   ├── factories/BuyerFactory.php
│   └── seeders/DatabaseSeeder.php
├── resources/
│   ├── css/app.css             # Tailwind 4 + DaisyUI 5 with garden/dim themes
│   ├── js/
│   │   ├── app.js              # Theme switcher
│   │   ├── charts.js           # Chart.js auto-renderer
│   │   └── bg-balls.js         # Animated bouncing balls
│   └── views/
│       ├── common/             # Layout, navbar, footer
│       ├── components/         # chart, product_box, product_gallery, review_card, pagination, admin_sidebar
│       ├── home/               # Landing page
│       ├── search/             # Search with filters
│       ├── account/            # Login, register, seller application, edit profile
│       ├── product/            # View, create, edit, confirm_order, view_receipt
│       ├── cart/               # Shopping cart with local qty
│       ├── orders/             # Buyer order history
│       ├── dashboard/          # Buyer + seller dashboards, seller orders
│       ├── address/            # Add/edit address
│       └── admin/              # Buyers, sellers, orders, applications, products
├── routes/web.php              # All 46 routes
├── tasks/                      # Group assignment docs
├── migration_info/             # Original React app reference
├── composer.json
├── package.json
└── vite.config.js
```

---

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+
- A Supabase project with the schema above applied

### Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Configure environment
cp .env.example .env
```

### 3. Configure `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD="your-password"
DB_SSLMODE=require
```

> If your password contains `#`, wrap it in double quotes.

### 4. Generate key & build

```bash
php artisan key:generate
npm install
npm run build
```

### 5. Run

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite HMR
npm run dev
```

Or all-in-one: `composer run dev`

---

## License

MIT — built as a learning project.
