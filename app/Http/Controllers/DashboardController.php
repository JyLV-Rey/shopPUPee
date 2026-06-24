<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function buyer(Buyer $buyer)
    {
        // Eager load all needed relations in one shot
        $buyer->load([
            'orders.items.product.seller',
            'orders.payment',
            'reviews.product',
        ]);

        // ── Aggregate stats ──────────────────────────────────────────────────
        $totalSpent = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyer->buyer_id)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->sum(DB::raw('product.price * order_item.quantity'));

        $totalOrdersPlaced = $buyer->orders->where('is_deleted', false)->count();

        $totalCancelledOrders = $buyer->orders
            ->where('is_deleted', false)
            ->where('status', 'Cancelled')
            ->count();

        $totalRefunds = $buyer->orders
            ->where('is_deleted', false)
            ->where('status', 'Refunded')
            ->count();

        // ── Chart data ────────────────────────────────────────────────────────
        // All non-deleted orders
        $orders = $buyer->orders->where('is_deleted', false);

        // 1. Top Categories Doughnut — most-purchased product categories
        $topCategories = $orders->flatMap(fn($o) => $o->items)
            ->groupBy(fn($item) => $item->product?->category ?? 'Unknown')
            ->map(fn($items) => $items->sum('quantity'))
            ->sortByDesc(fn($qty) => $qty)
            ->take(7);

        // 2. Spending Over Time Line — total spent per month
        $spendingOverTime = $orders
            ->whereNotIn('status', ['Cancelled', 'Refunded'])
            ->groupBy(fn($o) => $o->ordered_at?->format('Y-m'))
            ->map(fn($monthOrders) =>
                $monthOrders->flatMap(fn($o) => $o->items)
                    ->sum(fn($item) => ($item->product?->price ?? 0) * $item->quantity)
            )
            ->sortKeys();

        // 3. Spend by Category Bar — total spend per category
        $spendByCategory = $orders
            ->whereNotIn('status', ['Cancelled', 'Refunded'])
            ->flatMap(fn($o) => $o->items)
            ->groupBy(fn($item) => $item->product?->category ?? 'Unknown')
            ->map(fn($items) => $items->sum(fn($i) => ($i->product?->price ?? 0) * $i->quantity))
            ->sortByDesc(fn($v) => $v)
            ->take(8);

        // 4. Purchase Frequency Bar — how many orders per month
        $purchaseFrequency = $orders
            ->groupBy(fn($o) => $o->ordered_at?->format('Y-m'))
            ->map(fn($g) => $g->count())
            ->sortKeys();

        // 5. Top Products Bar — most-purchased products by quantity
        $topProducts = $orders->flatMap(fn($o) => $o->items)
            ->groupBy(fn($item) => $item->product?->name ?? 'Unknown')
            ->map(fn($items) => $items->sum('quantity'))
            ->sortByDesc(fn($qty) => $qty)
            ->take(5);

        // 6. Review Ratings Bar — distribution of review ratings (1-5 stars)
        $reviewRatings = collect([1, 2, 3, 4, 5])
            ->mapWithKeys(fn($star) => [
                $star => $buyer->reviews->where('rating', $star)->count(),
            ]);

        // 7. Preferred Sellers Doughnut — spend grouped by seller name
        $preferredSellers = $orders
            ->whereNotIn('status', ['Cancelled', 'Refunded'])
            ->flatMap(fn($o) => $o->items)
            ->groupBy(fn($item) => $item->product?->seller?->seller_name ?? 'Unknown')
            ->map(fn($items) => $items->sum(fn($i) => ($i->product?->price ?? 0) * $i->quantity))
            ->sortByDesc(fn($v) => $v)
            ->take(7);

        // 8. Payment Methods Pie — order count by payment method
        $paymentMethods = $orders
            ->mapWithKeys(fn($o) => [$o->order_id => $o->payment?->payment_method ?? 'Unknown'])
            ->groupBy(fn($method) => $method)
            ->map(fn($g) => $g->count());

        // 9. Most Expensive Items Bar — top 5 items by unit price
        $mostExpensiveItems = $orders->flatMap(fn($o) => $o->items)
            ->filter(fn($i) => $i->product !== null)
            ->unique(fn($i) => $i->product_id)
            ->sortByDesc(fn($i) => (float) $i->product->price)
            ->take(5)
            ->map(fn($i) => [
                'name'  => $i->product->name,
                'price' => (float) $i->product->price,
            ]);

        // 10. Least Expensive Items Bar — bottom 5 items by unit price
        $leastExpensiveItems = $orders->flatMap(fn($o) => $o->items)
            ->filter(fn($i) => $i->product !== null)
            ->unique(fn($i) => $i->product_id)
            ->sortBy(fn($i) => (float) $i->product->price)
            ->take(5)
            ->map(fn($i) => [
                'name'  => $i->product->name,
                'price' => (float) $i->product->price,
            ]);

        return view('dashboard.buyer', compact(
            'buyer',
            'totalSpent',
            'totalOrdersPlaced',
            'totalCancelledOrders',
            'totalRefunds',
            'topCategories',
            'spendingOverTime',
            'spendByCategory',
            'purchaseFrequency',
            'topProducts',
            'reviewRatings',
            'preferredSellers',
            'paymentMethods',
            'mostExpensiveItems',
            'leastExpensiveItems',
        ));
    }

    public function seller(Seller $seller)
    {
        // Eager load everything needed (including order.buyer for top-buyers chart)
        $seller->load([
            'buyer',
            'products.images',
            'products.orderItems.order.buyer',
            'products.reviews',
        ]);

        $buyer = $seller->buyer;

        // ── Build a lookup: product_id => [price, name, category] ────────────
        // Because products.orderItems does NOT auto-populate orderItem->product,
        // we carry the product info forward when flattening.
        $allOrderItems = $seller->products->flatMap(function ($product) {
            return $product->orderItems->map(function ($oi) use ($product) {
                $oi->_price    = (float) $product->price;
                $oi->_name     = $product->name;
                $oi->_category = $product->category;
                return $oi;
            });
        });

        $activeOrderItems = $allOrderItems->filter(
            fn($oi) => $oi->order
                && ! $oi->order->is_deleted
                && ! in_array($oi->order->status, ['Cancelled', 'Refunded'])
        );

        // ── Aggregate stats ──────────────────────────────────────────────────
        $totalProductsListed = $seller->products->count();
        $totalItemsSold      = $activeOrderItems->sum('quantity');
        $totalRevenue        = $activeOrderItems->sum(fn($oi) => $oi->_price * $oi->quantity);

        // Unique orders from all seller products
        $sellerOrders = $allOrderItems
            ->map(fn($oi) => $oi->order)
            ->filter()
            ->unique('order_id');

        $totalCancelled = $sellerOrders->where('status', 'Cancelled')->count();
        $totalRefunded  = $sellerOrders->where('status', 'Refunded')->count();

        $allReviews    = $seller->products->flatMap(fn($p) => $p->reviews);
        $averageRating = $allReviews->count() > 0
            ? round($allReviews->avg('rating'), 1)
            : null;

        // ── Chart data ────────────────────────────────────────────────────────
        // 1. Top Selling Products (qty sold per product)
        $topSellingProducts = $seller->products
            ->mapWithKeys(fn($p) => [
                $p->product_id => [
                    'name'     => $p->name,
                    'quantity' => $p->orderItems
                        ->filter(fn($oi) => $oi->order
                            && ! $oi->order->is_deleted
                            && ! in_array($oi->order->status, ['Cancelled', 'Refunded']))
                        ->sum('quantity'),
                ],
            ])
            ->sortByDesc(fn($entry) => $entry['quantity'])
            ->take(5);

        // 2. Top Categories Bar (units sold per category)
        $topCategories = $seller->products
            ->groupBy('category')
            ->map(fn($prods) =>
                $prods->flatMap(fn($p) => $p->orderItems)
                    ->filter(fn($oi) => $oi->order
                        && ! $oi->order->is_deleted
                        && ! in_array($oi->order->status, ['Cancelled', 'Refunded']))
                    ->sum('quantity')
            )
            ->sortByDesc(fn($qty) => $qty)
            ->take(7);

        // 3. Monthly Earnings Line Chart
        $monthlyEarnings = $activeOrderItems
            ->groupBy(fn($oi) => $oi->order?->ordered_at?->format('Y-m'))
            ->map(fn($items) => $items->sum(fn($oi) => $oi->_price * $oi->quantity))
            ->sortKeys();

        // 4. Purchase Frequency Bar (unique orders per month)
        $purchaseFrequency = $sellerOrders
            ->filter(fn($o) => ! $o->is_deleted)
            ->groupBy(fn($o) => $o->ordered_at?->format('Y-m'))
            ->map(fn($g) => $g->count())
            ->sortKeys();

        // 5. Top Reviewed Products
        $topReviewedProducts = $seller->products
            ->mapWithKeys(fn($p) => [
                $p->product_id => [
                    'name'   => $p->name,
                    'count'  => $p->reviews->count(),
                ],
            ])
            ->sortByDesc(fn($entry) => $entry['count'])
            ->take(5);

        // 6. Earnings by Category Doughnut
        $earningsByCategory = $seller->products
            ->groupBy('category')
            ->map(function ($prods) {
                return $prods->flatMap(function ($p) {
                    $price = (float) $p->price;
                    return $p->orderItems->map(function ($oi) use ($price) {
                        $oi->_price = $price;
                        return $oi;
                    });
                })
                ->filter(fn($oi) => $oi->order
                    && ! $oi->order->is_deleted
                    && ! in_array($oi->order->status, ['Cancelled', 'Refunded']))
                ->sum(fn($oi) => $oi->_price * $oi->quantity);
            })
            ->sortByDesc(fn($v) => $v);

        // 7. Top Buyers Bar — buyers by spend at this store
        $topBuyers = $activeOrderItems
            ->groupBy(function ($oi) {
                $b = $oi->order?->buyer;
                return $b ? trim($b->first_name . ' ' . $b->last_name) : 'Unknown';
            })
            ->map(fn($items) => $items->sum(fn($oi) => $oi->_price * $oi->quantity))
            ->sortByDesc(fn($v) => $v)
            ->take(5);

        // 8. Most Expensive Products
        $mostExpensiveProducts = $seller->products
            ->sortByDesc(fn($p) => (float) $p->price)
            ->take(5)
            ->mapWithKeys(fn($p) => [
                $p->product_id => [
                    'name'  => $p->name,
                    'price' => (float) $p->price,
                ],
            ]);

        // 9. Least Expensive Products (active only)
        $leastExpensiveProducts = $seller->products
            ->filter(fn($p) => ! $p->is_deleted)
            ->sortBy(fn($p) => (float) $p->price)
            ->take(5)
            ->mapWithKeys(fn($p) => [
                $p->product_id => [
                    'name'  => $p->name,
                    'price' => (float) $p->price,
                ],
            ]);

        // 10. Order Status Distribution
        $orderStatusDist = $sellerOrders
            ->filter(fn($o) => ! $o->is_deleted)
            ->groupBy('status')
            ->map(fn($g) => $g->count());

        // Low Stock (quantity < 5, not deleted)
        $lowStockProducts = $seller->products
            ->filter(fn($p) => ! $p->is_deleted && $p->quantity < 5)
            ->sortBy('quantity');

        return view('dashboard.seller', compact(
            'seller',
            'buyer',
            'totalProductsListed',
            'totalItemsSold',
            'totalRevenue',
            'totalCancelled',
            'totalRefunded',
            'averageRating',
            'topSellingProducts',
            'topCategories',
            'monthlyEarnings',
            'purchaseFrequency',
            'topReviewedProducts',
            'earningsByCategory',
            'topBuyers',
            'mostExpensiveProducts',
            'leastExpensiveProducts',
            'orderStatusDist',
            'lowStockProducts',
        ));
    }
}
