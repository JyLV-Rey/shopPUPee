# Task: Auth System — Login, Register, Seller Application

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/AuthController.php`
**Views:** `resources/views/account/` (create this folder)
**Pages in `migration_info/`:** `Account.md`

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/account/login` | `showLogin()` | `account.login` |
| POST | `/account/login` | `login()` | — (redirect) |
| GET | `/account/create` | `showCreateAccount()` | `account.create` |
| POST | `/account/create` | `createAccount()` | — (redirect) |
| GET | `/account/create/seller` | `showCreateSeller()` | `account.create_seller` |
| POST | `/account/create/seller` | `createSeller()` | — (redirect) |
| POST | `/logout` | `logout()` | — (redirect) |

---

## Views to create

### `resources/views/account/login.blade.php`

**URL:** `/account/login`

**Query params your view should handle:**
- `?redirect=true` — show a banner: *"Please log in to continue"*
- `?accountCreated=true` — show a banner: *"Account created successfully!"*

**What it displays (per `migration_info/Account.md` → LoginAccount):**
- Full-screen layout with a centered card
- App logo / branding at top
- Email input
- Password input
- "Login" submit button
- Link to `/account/create` — *"No account yet? Sign up here"*
- Error display for invalid credentials

**Form action:** `POST /account/login`

### `resources/views/account/create.blade.php`

**URL:** `/account/create`

**What it displays (per `migration_info/Account.md` → CreateAccount):**
- Registration form with fields: **first name, last name, email, phone, password**
- "Create Account" submit button
- Link to `/account/login` — *"Already have an account? Log in"*

**Form action:** `POST /account/create`
**On success:** redirect to `/account/login?accountCreated=true`

### `resources/views/account/create_seller.blade.php`

**URL:** `/account/create/seller`

**What it displays (per `migration_info/Account.md` → CreateSellerAccount):**
- Form explaining this is a seller application
- Fields: **seller/store name, valid ID image URL**
- Address fields: **street, city, province, barangay, region, postal code, unit/floor (optional), additional notes (optional)**
- "Submit Application" button
- Note: creating a seller application inserts into `seller_application` table, NOT `seller` directly

**Form action:** `POST /account/create/seller`
**On success:** redirect to `/` with a success message

---

## Controller methods to implement

### `showLogin(Request $request)`
- Pass `$redirect` (boolean) and `$accountCreated` (boolean) from query params to the view.

### `login(Request $request)`
- Validate: `email` (required), `password` (required)
- Query: `Buyer::where('email', $request->email)->where('is_deleted', false)->first()`
- **Important:** The original app uses **plaintext password comparison** (`$buyer->password === $request->password`) — match this behavior
- If matched: `Auth::login($buyer)`, then redirect to `route('home')`
- If not matched: redirect back with error *"Invalid Credentials"*
- If `is_deleted`: redirect back with error *"Account deactivated"*

### `showCreateAccount()`
- Return the registration view.

### `createAccount(Request $request)`
- Validate: `first_name`, `last_name`, `email` (unique), `phone`, `password`
- Insert into `buyer` table: `Buyer::create([...])`
- Redirect to `route('account.login', ['accountCreated' => true])`

### `showCreateSeller()`
- Return the seller application view.

### `createSeller(Request $request)`
- Requires the user to be logged in (should already be in guest middleware? Actually this route is under `guest` middleware — wait, check routes. The `/account/create/seller` route is under `guest` middleware, so the user shouldn't be logged in... but the original React app creates a seller application for an existing buyer. **Discuss with your team whether this should be moved to authenticated routes.**)
- For now: create a `Buyer` first if not exists, or require login before applying
- Validate and insert into `seller_application` table
- Also insert into `address` table for the provided address
- Redirect to home with success message

### `logout(Request $request)`
- `Auth::logout()`
- `$request->session()->invalidate()`
- `$request->session()->regenerateToken()`
- Redirect to `route('home')`

---

## Models you'll use

| Model | Table | Key |
|---|---|---|
| `Buyer` | `buyer` | `buyer_id` |
| `SellerApplication` | `seller_application` | `application_id` |
| `Address` | `address` | `address_id` |

## Layout

All views should `@extends('common.index')` and use `@section('title', '...')` / `@section('content')`.

Use DaisyUI components: `card`, `input input-bordered`, `btn btn-primary`, `label`, `select`.
