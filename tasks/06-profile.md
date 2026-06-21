# Task: Profile & Address Management

**Assignee:** \_\_\_\_\_\_\_\_\_\_
**Controller:** `app/Http/Controllers/ProfileController.php`
**Views:** `resources/views/account/` and `resources/views/address/` (create folders)
**Pages in `migration_info/`:** `Account.md` (EditBuyer, EditSeller sections), `GlobalFeatures.md` (EditAddress, AddAddress sections)

---

## Routes you own

| Method | URL | Controller Method | View |
|---|---|---|---|
| GET | `/edit/buyer` | `editBuyer(Request $request)` | `edit.buyer` |
| POST | `/edit/buyer` | `updateBuyer(Request $request)` | — (redirect) |
| GET | `/edit/seller` | `editSeller(Request $request)` | `edit.seller` |
| POST | `/edit/seller` | `updateSeller(Request $request)` | — (redirect) |
| GET | `/edit/address` | `editAddress(Request $request)` | `edit.address` |
| POST | `/edit/address` | `updateAddress(Request $request)` | — (redirect) |
| GET | `/add/address` | `addAddress()` | `address.add` |
| POST | `/add/address` | `storeAddress(Request $request)` | — (redirect) |

All require authentication (`check.user` middleware).

---

## Views to create

### `resources/views/account/edit_buyer.blade.php`

**URL:** `/edit/buyer`

**What it displays:**
A form to edit the logged-in buyer's profile information.

**Pre-filled fields (from `Auth::user()`):**
- First name (text input)
- Last name (text input)
- Email (text input, disabled or with warning — changing email may affect login)
- Phone (text input)

**Form action:** `POST /edit/buyer`
**On success:** redirect back with *"Profile updated successfully"*

---

### `resources/views/account/edit_seller.blade.php`

**URL:** `/edit/seller`

**What it displays:**
A form to edit the seller's store information.

**Requirements:**
- User must have a seller account (`Auth::user()->seller` must exist)
- If not a seller, redirect with error

**Pre-filled fields (from `Auth::user()->seller`):**
- Store name (`seller_name` — text input)

**Form action:** `POST /edit/seller`
**On success:** redirect back with *"Store updated successfully"*

---

### `resources/views/address/edit.blade.php`

**URL:** `/edit/address`

**What it displays:**
A form to edit an existing saved address.

**Query approach:** The address is loaded based on the `addressId` query param or by passing the Address model. Since there's no route-model binding here, use the query param:
```php
$address = Address::where('address_id', $request->query('addressId'))
    ->where('buyer_id', Auth::user()->buyer_id)
    ->firstOrFail();
```

**Pre-filled fields:**
- Street (text input)
- City (text input)
- Province (text input)
- Barangay (text input)
- Region (text input)
- Postal code (text input)
- Unit/floor (text input, optional)
- Additional notes (textarea, optional)

**Form action:** `POST /edit/address`
**On success:** redirect back with *"Address updated successfully"*

---

### `resources/views/address/add.blade.php`

**URL:** `/add/address`

**What it displays:**
A form to add a new shipping address, identical field layout to edit but empty.

**Fields (same as edit):**
- Street (required), City (required), Province (required), Barangay (required), Region (required)
- Postal code (optional)
- Unit/floor (optional)
- Additional notes (optional)

**Form action:** `POST /add/address`
**On success:** redirect to previous page with *"Address added successfully"*

---

## Controller methods to implement

### `editBuyer(Request $request)`
- Just return the view — the form reads from `Auth::user()`

### `updateBuyer(Request $request)`
- Validate: `first_name`, `last_name`, `email`, `phone`
- Update: `Auth::user()->update([...])`
- Redirect back with success message

### `editSeller(Request $request)`
- Check `Auth::user()->seller` exists, redirect if not
- Return view with `$seller`

### `updateSeller(Request $request)`
- Validate: `seller_name`
- Check seller exists
- `Auth::user()->seller->update(['seller_name' => $request->seller_name])`
- Redirect back with success

### `editAddress(Request $request)`
- Load address by `addressId` query param, scoped to the current buyer
- `$address = Address::where('address_id', $request->query('addressId'))->where('buyer_id', Auth::user()->buyer_id)->firstOrFail()`
- Return view with `$address`

### `updateAddress(Request $request)`
- Validate all address fields
- Load address same as edit
- `$address->update([...])`
- Redirect back with success

### `addAddress()`
- Return the add address view

### `storeAddress(Request $request)`
- Validate all address fields
- `Address::create([...] + ['buyer_id' => Auth::user()->buyer_id])`
- Redirect back with success

---

## Models you'll use

| Model | Table | Relations |
|---|---|---|
| `Buyer` | `buyer` | `seller()` (HasOne), `addresses()` (HasMany) |
| `Seller` | `seller` | `buyer()` (BelongsTo) |
| `Address` | `address` | `buyer()` (BelongsTo) |

## Layout

`@extends('common.index')` with `@section('title', ...)` / `@section('content')`.

Use DaisyUI components: `card`, `input input-bordered`, `textarea textarea-bordered`, `btn btn-primary`, `label`, `select`.
