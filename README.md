
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
- **Seller applications** — buyers apply to become sellers; admin approves
- **Seller storefronts** — sellers create, edit, and manage products
- **Shopping cart** — add items, adjust quantities, proceed to checkout
- **Order management** — place orders, view receipts, track history
- **Admin panel** — manage buyers, sellers, products, orders, and seller applications
- **Soft-delete system** — entities are hidden rather than destroyed (`is_deleted` flag)
- **Role-based access** — guest, authenticated buyer, seller, and admin tiers

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.3+, Laravel 13 |
| **Frontend** | Blade templates, Tailwind CSS 4, DaisyUI 5 |
| **Build** | Vite 8 + `laravel-vite-plugin` |
| **Database** | Supabase (PostgreSQL 15+) |
| **Auth** | Session-based against `buyer` table |

---

## Database Schema

The database lives on **Supabase** and uses PostgreSQL. Below is the entity map — every table has a corresponding Eloquent model in `app/Models/`.

### Core Entities

```
buyer ──┬── seller             (one buyer → one seller)
        ├── address            (one buyer → many addresses)
        ├── cartitem           (one buyer → many cart items)
        ├── review             (one buyer → many reviews)
        ├── seller_application (one buyer → many applications)
        └── order              (one buyer → many orders)

seller ──┬── product           (one seller → many products)
         └── seller_application (one seller → one application)

product ──┬── product_image    (one product → many images)
          ├── price_history    (one product → many price records)
          ├── review           (one product → many reviews)
          ├── cartitem         (one product → many cart items)
          └── order_item       (one product → many order items)

order ──┬── order_item         (one order → many items)
        ├── payment            (one order → one payment)
        ├── delivery           (one order → one delivery)
        ├── cancel             (one order → one cancel request)
        └── refund             (one order → one refund request)
```

### Key Design Notes

- **Authentication** is handled against the `buyer` table itself — no separate `users` table. Every buyer has `email` + `password` for login.
- **Soft deletes** use an `is_deleted` boolean column across most entities instead of Laravel's built-in `SoftDeletes` trait.
- **Admin status** is a simple `is_admin` boolean on the `buyer` table.
- **Seller applications** are approved by admins — when approved, a `seller` row is created linked to the applicant's `buyer_id`.
- **Custom PostgreSQL enums** are used for `order.status`, `delivery.delivery_status`, `payment.payment_method`, `payment.payment_status`, `seller_application.status`, and `refund.refund_status`.

### All Tables (15)

| Table | PK | Foreign Keys |
|---|---|---|
| `buyer` | `buyer_id` | — |
| `seller` | `seller_id` | `buyer_id`, `address_id`, `application_id` |
| `seller_application` | `application_id` | `buyer_id`, `address_id` |
| `product` | `product_id` | `seller_id` |
| `product_image` | `image_id` | `product_id` |
| `price_history` | `history_id` | `product_id` |
| `cartitem` | `cart_item_id` | `buyer_id`, `product_id` |
| `order` | `order_id` | `buyer_id` |
| `order_item` | `order_item_id` | `order_id`, `product_id` |
| `payment` | `payment_id` | `order_id` |
| `delivery` | `delivery_id` | `order_id`, `buyer_address_id` |
| `address` | `address_id` | `buyer_id` |
| `review` | `review_id` | `product_id`, `buyer_id` |
| `cancel` | `cancel_id` | `order_id` |
| `refund` | `refund_id` | `order_id` |

---

## Routes

The app exposes **33 named routes** organized by access level:

| Group | Routes |
|---|---|
| **🌐 Public** | `/`, `/search`, `/product/view` |
| **👤 Guest** | `/account/login`, `/account/create`, `/account/create/seller` |
| **🔒 Authenticated** | `/dashboard/*`, `/cart`, `/orders`, `/product/*`, `/edit/*`, `/add/address` |
| **🛡️ Admin** | `/dashboard/admin/buyer`, `/dashboard/admin/seller`, `/dashboard/admin/order`, `/dashboard/admin/application`, `/dashboard/admin/product` |

All routes are defined in `routes/web.php` with named middlewares:
- `check.user` — redirects unauthenticated users to login, checks `is_deleted`
- `check.admin` — requires authentication + `is_admin = true`

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

Update your `.env` with your Supabase database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD="your-password-with-#-if-needed"
DB_SSLMODE=require
```

> **Note:** If your password contains `#`, wrap it in double quotes.

### 4. Generate app key & build assets

```bash
php artisan key:generate
npm install
npm run build
```

### 5. Run the dev server

For development with hot-reloading:

```bash
# Terminal 1 — Laravel dev server
php artisan serve

# Terminal 2 — Vite HMR
npm run dev
```

Or use the all-in-one dev script:

```bash
composer run dev
```

This fires up Laravel (`:8000`), queue listener, logs, and Vite concurrently.

---

## Middleware

| Middleware | Alias | What it does |
|---|---|---|
| `CheckUser` | `check.user` | Redirects to login if unauthenticated; logs out deactivated accounts |
| `CheckAdmin` | `check.admin` | Requires authentication + `is_admin = true`; 403 otherwise |

Both are registered in `bootstrap/app.php`.

---

## Models

All 15 Eloquent models live in `app/Models/` and use PHP 8 `#[Table]` attributes to declare table name, primary key, and key type:

```php
#[Table('buyer', 'buyer_id', 'int')]
class Buyer extends Authenticatable { ... }
```

| Model | Table | Extends |
|---|---|---|
| `Buyer` | `buyer` | `Authenticatable` (auth-enabled) |
| `Seller`, `SellerApplication` | `seller`, `seller_application` | `Model` |
| `Product`, `ProductImage`, `PriceHistory` | `product`, etc. | `Model` |
| `CartItem`, `Order`, `OrderItem` | `cartitem`, `order`, `order_item` | `Model` |
| `Payment`, `Delivery`, `Address` | `payment`, `delivery`, `address` | `Model` |
| `Review`, `Cancel`, `Refund` | `review`, `cancel`, `refund` | `Model` |

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/    # 9 controllers (Home, Auth, Dashboard, Product, Cart, Order, Profile, Admin, Search)
│   │   └── Middleware/      # CheckUser, CheckAdmin
│   └── Models/              # 15 Eloquent models
├── bootstrap/app.php        # App config + middleware registration
├── config/                  # Laravel config files
├── database/
│   ├── factories/           # BuyerFactory
│   ├── migrations/          # Default Laravel migrations
│   └── seeders/             # DatabaseSeeder
├── migration_info/          # Reference docs from the original React app
├── resources/
│   ├── css/app.css          # Tailwind + DaisyUI stylesheet
│   ├── js/app.js            # Vite entry point
│   └── views/               # Blade templates
├── routes/
│   └── web.php              # All 33 routes
├── composer.json
├── package.json
└── vite.config.js           # Vite + Laravel plugin + Tailwind + DaisyUI
```

---

## License

MIT — built as a learning project.
