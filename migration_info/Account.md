# Account Pages

## LoginAccount (`/account/login`)

**File:** `src/Pages/Account/LoginAccount/LoginAccount.jsx`

### What It Does
Buyer login form. Authenticates against the `buyer` table (plaintext password comparison). On success, sets `userId`, `userEmail`, `userFirstName`, `userLastName`, `userSellerName`, `userSellerId` into `UserContext` (persisted to `localStorage`).

### URL Interaction
**Query params:**
- `?redirect=true` — shows a "Please log in to continue" banner (set by `CheckCredentials` guard).
- `?accountCreated=true` — shows "Account Creation Successful!" banner (set by `CreateAccount` page after registration).

On successful login, navigates to `/`.

### Supabase Queries
```sql
SELECT buyer_id, first_name, last_name, email, password,
  seller (seller_name, seller_id)
FROM buyer
WHERE email = '<email>' AND is_deleted = false
```
- `maybeSingle()` — returns one or none.
- If `data.password !== password` → shows "Invalid Credentials" error.

### What It Can Do
1. Email/password login (plaintext — no hashing).
2. Fetches seller data and stores it in context.
3. Shows redirect or success banners from URL params.
4. Link to `/account/create` for new users.

### Typical UI
- Full-screen view with background splash image + dark overlay.
- Centered white/glass card with logo, "PolyPlaza" heading, tagline.
- Email + password inputs with amber borders.
- Login button + "No account yet? sign up here" link.

---

## CreateAccount (`/account/create`)

**File:** `src/Pages/Account/CreateAccount/CreateAccount.jsx`

### What It Does
Buyer registration form. Inserts a new row into the `buyer` table.

### URL Interaction
On success navigates to `/account/login?accountCreated=true`.

### Supabase Queries
```sql
INSERT INTO buyer (first_name, last_name, phone, email, password)
VALUES (<...>)
RETURNING *
```
- No explicit duplicate check — DB unique constraint on email triggers the "Email may already exist" error.

### What It Can Do
1. Register a new buyer with first name, last name, email, phone, password.
2. Validates all fields are filled and passwords match.
3. Error handling for duplicate email.
4. Link back to login.

### Typical UI
- Split-form layout: left column = personal info fields, right column reserved for address info (currently empty/unused).
- White glass card on splash background.
- "Create Account" button.
- Error/success banners.

---

## CreateSellerAccount (`/account/create/seller`)

**File:** `src/Pages/Account/CreateSellerAccount/CreateSellerAccount.jsx`

### What It Does
Seller application form. Submits a `seller_application` with store name, valid ID URL, and address. Checks for existing pending/rejected applications before allowing re-submission.

### URL Interaction
Static route — no query params. No navigation on success (stays on page showing success message).

### Supabase Queries
**Fetch latest application (on mount):**
```sql
SELECT status FROM seller_application
WHERE buyer_id = <userId>
ORDER BY application_date DESC
LIMIT 1
```

**Submit application:**
```sql
INSERT INTO seller_application (valid_id_url, buyer_id, seller_name, address_id)
VALUES (<...>)
```

### What It Can Do
1. Check existing application status (Pending → disabled form; Rejected → can reapply; Approved → success message).
2. Submit new application with store name, valid ID URL, and address (via AddressBook component).
3. Clear success message when user changes input.
4. Disable submit while pending.

### Typical UI
- Green-themed glass card on splash background.
- Two fields: Seller Name, URL for Valid ID.
- AddressBook component for selecting address.
- Submit button (disabled if pending). Error/success banners.

---

## EditBuyer (`/edit/buyer`)

**File:** `src/Pages/Account/EditAccount/EditBuyer.jsx`

### What It Does
Lets a buyer update their profile info (first name, last name, email, phone, password) and manage addresses.

### URL Interaction
**Query param:** `buyerId` (required)
```
/edit/buyer?buyerId=<id>
```

### Supabase Queries
**Fetch buyer:**
```sql
SELECT first_name, last_name, email, phone, password
FROM buyer WHERE buyer_id = <buyerId>
```

**Update buyer:**
```sql
UPDATE buyer SET first_name, last_name, email, phone, password
WHERE buyer_id = <buyerId>
```

### What It Can Do
1. Pre-fill form with current buyer data.
2. Update all personal fields (requires password confirmation).
3. Update UserContext if editing own profile (`userId == buyerId`).
4. Address management via embedded `AddressBook` component (add/edit/delete).
5. Navigate to `/` on success.

---

## EditSeller (`/edit/seller`)

**File:** `src/Pages/Account/EditAccount/EditSeller.jsx`

### What It Does
Lets a seller update their store name and address.

### URL Interaction
```
/edit/seller?sellerId=<id>
```

### Supabase Queries
**Fetch seller:**
```sql
SELECT seller_name, address_id, buyer_id
FROM seller WHERE seller_id = <sellerId>
```

**Update seller:**
```sql
UPDATE seller SET seller_name, address_id
WHERE seller_id = <sellerId>
```

### What It Can Do
1. Pre-fill with current store name and address.
2. Update store name.
3. Change address via AddressBook.
4. Updates `userSellerName` in UserContext.
