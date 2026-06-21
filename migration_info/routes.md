# PolyPlaza Route Map

All routes are defined in `src/App.jsx` inside a `<Routes>` block wrapped in `<BrowserRouter>` at `src/main.jsx`. The app has a persistent `<NavBar />` and `<Footer />` surrounding every page.

---

## Route Table

| Path | Page Component | Folder | Purpose |
|------|---------------|--------|---------|
| `/` | `HomePage` | `HomePage/` | Landing page — search bar, categories, trending & featured products |
| `/search` | `SearchPage` | `SearchPage/` | Search results with query params for filtering/sorting |
| `/account/login` | `LoginAccount` | `Account/LoginAccount/` | Buyer login form |
| `/account/create` | `CreateAccount` | `Account/CreateAccount/` | Buyer registration form |
| `/account/create/seller` | `CreateSellerAccount` | `Account/CreateSellerAccount/` | Seller application form |
| `/dashboard/buyer` | `BuyerDashboard` | `Dashboard/BuyerDashboard/` | Buyer analytics dashboard |
| `/dashboard/seller` | `SellerDashboard` | `Dashboard/SellerDashbord/` | Seller analytics dashboard |
| `/product/view` | `ViewProduct` | `ViewProduct/` | Single product detail page |
| `/cart` | `CartPage` | `CartPage/` | Shopping cart management |
| `/product/confirm_order` | `ConfirmOrderPage` | `BuyPage/` | Order confirmation / checkout |
| `/product/view_receipt` | `ViewReceipt` | `ViewReceipt/` | Order receipt after purchase |
| `/product/create` | `CreateProduct` | `Product/` | Seller creates a new product |
| `/product/edit` | `EditProduct` | `Product/` | Seller edits an existing product |
| `/orders` | `ViewOrders` | `ViewOrders/` | Buyer views their order history |
| `/edit/buyer` | `EditBuyer` | `Account/EditAccount/` | Edit buyer profile info |
| `/edit/seller` | `EditSeller` | `Account/EditAccount/` | Edit seller store info |
| `/edit/address` | `EditAddress` | `GlobalFeatures/` | Edit a saved address |
| `/add/address` | `AddAddress` | `GlobalFeatures/` | Add a new address |
| `/dashboard/admin/buyer` | `BuyersView` | `Dashboard/Admin/BuyersView/` | Admin — list & disable buyers |
| `/dashboard/admin/seller` | `SellersView` | `Dashboard/Admin/SellersView/` | Admin — list & disable sellers |
| `/dashboard/admin/order` | `OrdersView` | `Dashboard/Admin/OrdersView/` | Admin — list & disable orders |
| `/dashboard/admin/application` | `ApplicationsView` | `Dashboard/Admin/ApplicationView/` | Admin — approve/reject seller apps |
| `/dashboard/admin/product` | `ProductsView` | `Dashboard/Admin/ProductsView/` | Admin — list & disable products |

---

## Shared Components (Non-route but critical)

| Component | File | Purpose |
|-----------|------|---------|
| `UserProvider` | `UserContext.jsx` | React context wrapping the whole app: provides `userId`, `userFirstName`, `userLastName`, `userEmail`, `userSellerId`, `userSellerName` persisted to `localStorage` |
| `CheckCredentials` | `CheckCredentials.jsx` | Route guard — if `userId` is null, redirects to `/account/login?redirect=true` |
| `NavBar` | `GlobalFeatures/NavBar` | Persistent top navigation (always visible) |
| `Footer` | `GlobalFeatures/Footer` | Persistent footer (always visible) |
| `ScrollToTop` | `GlobalFeatures/ScrollToTop` | Scrolls to top on route change |
| `supabase` | `supabase.js` | Singleton Supabase client using `VITE_SUPABASE_URL` and `VITE_SUPABASE_ANON_KEY` env vars |

---

## URL Parameter Conventions

Most routes that display specific data use **query parameters** (not path params):

- **`buyerId`** — used on `/dashboard/buyer`, `/orders`, `/edit/buyer`
- **`sellerId`** — used on `/dashboard/seller`, `/product/create`, `/edit/seller`
- **`productId`** — used on `/product/view`, `/product/edit`, `/product/confirm_order`
- **`orderId`** — used on `/product/view_receipt`
- **`addressId`** — used on `/edit/address`
- **`searchTerm`, `searchCategory`, `sortBy`, `isDescending`, `maxPrice`, `searchStore`** — all used on `/search`
- **`cartItems`** — comma-separated `cart_item_id` list on `/product/confirm_order`
- **`redirect`** — boolean flag on `/account/login` to show "please log in" banner
- **`accountCreated`** — boolean flag on `/account/login` to show success banner
- **`justOrdered`** — boolean flag on `/product/view_receipt` to show a header confirmation

---

## Auth Flow & Guarding

1. **Login** (`/account/login`) — authenticates against `buyer` table (plaintext password compare). Stores session in `UserContext` (→ `localStorage`).
2. **Route guard** — `CheckCredentials` wraps protected pages (Cart, Dashboard, Orders, Receipt, etc.). If no `userId`, it `<Navigate>`s to `/account/login?redirect=true`.
3. **Deleted-entity gates** — many pages check `is_deleted` on the loaded entity and render a "disabled" screen rather than the normal UI.
