# GlobalFeatures (Routed Pages)

These components live in `src/GlobalFeatures/` but are registered as routes in `App.jsx`.

---

## AddAddress (`/add/address`)

**File:** `src/GlobalFeatures/AddAddress.jsx`

### What It Does
Form to add a new address to the user's address book.

### URL Interaction
Static route — no query params. On success, navigates back (`navigate(-1)`).

### Supabase Queries
```sql
INSERT INTO address (buyer_id, unit_floor, postal_code, street, barangay, province, city, region)
VALUES (<userId>, ...)
```

### What It Can Do
1. Add address with required fields: street, city, region.
2. Optional fields: unit/floor, postal code, barangay, province.
3. Validates required fields before submission.

### Typical UI
- Green-themed form card on emerald-50 background.
- 7 text fields stacked vertically.
- "Save Address" button.

---

## EditAddress (`/edit/address`)

**File:** `src/GlobalFeatures/EditAddress.jsx`

### What It Does
Edit or delete an existing address.

### URL Interaction
```
/edit/address?addressId=<id>
```
On save or delete, navigates back (`navigate(-1)`).

### Supabase Queries

**Fetch address (on mount):**
```sql
SELECT unit_floor, postal_code, street, barangay, province, city, region
FROM address WHERE address_id = <addressId>
```

**Update address:**
```sql
UPDATE address SET ... WHERE address_id = <addressId>
```

**Delete address:**
```sql
DELETE FROM address WHERE address_id = <addressId>
```

### What It Can Do
1. Pre-fill form with address data from DB.
2. Update all address fields.
3. Delete address (with confirmation prompt).
4. Validates required fields (street, city, region).

### Typical UI
- Same form layout as AddAddress but with splash background.
- Two buttons: "Save Changes" (green) + "Delete Address" (red).
- Error banner at top.
