<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function buyer(Buyer $buyer)
    {
        $buyerId = $buyer->buyer_id;

        // ── Aggregate stats (pure SQL — fast on PostgreSQL) ───────────────
        $totalSpent = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->sum(DB::raw('product.price * order_item.quantity'));

        $totalOrdersPlaced = DB::table('order')
            ->where('buyer_id', $buyerId)
            ->whereRaw('NOT is_deleted')
            ->count();

        $totalCancelledOrders = DB::table('order')
            ->where('buyer_id', $buyerId)
            ->whereRaw('NOT is_deleted')
            ->where('status', 'Cancelled')
            ->count();

        $totalRefunds = DB::table('order')
            ->where('buyer_id', $buyerId)
            ->whereRaw('NOT is_deleted')
            ->where('status', 'Refunded')
            ->count();

        // ── 1. Top Categories — most-purchased product categories ─────────
        $topCategories = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select('product.category', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->limit(7)
            ->get()
            ->pluck('total', 'category');

        // ── 2. Spending Over Time — per month ─────────────────────────────
        $spendingOverTime = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select(DB::raw("to_char(order.ordered_at, 'YYYY-MM') as month"), DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy(DB::raw("to_char(order.ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // ── 3. Spend by Category ──────────────────────────────────────────
        $spendByCategory = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select('product.category', DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get()
            ->pluck('total', 'category');

        // ── 4. Purchase Frequency — orders per month ──────────────────────
        $purchaseFrequency = DB::table('order')
            ->where('buyer_id', $buyerId)
            ->whereRaw('NOT is_deleted')
            ->select(DB::raw("to_char(ordered_at, 'YYYY-MM') as month"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("to_char(ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // ── 5. Top Products by quantity ───────────────────────────────────
        $topProducts = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select('product.name', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total', 'name');

        // ── 6. Review Ratings distribution ────────────────────────────────
        $reviewCounts = DB::table('review')
            ->where('buyer_id', $buyerId)
            ->select('rating', DB::raw('COUNT(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->keyBy('rating');

        $reviewRatings = collect([1, 2, 3, 4, 5])
            ->mapWithKeys(fn($star) => [$star => (int) ($reviewCounts->get($star)?->total ?? 0)]);

        // ── 7. Preferred Sellers by spend ─────────────────────────────────
        $preferredSellers = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->join('seller', 'product.seller_id', '=', 'seller.seller_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereRaw('NOT seller.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select('seller.seller_name', DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy('seller.seller_name')
            ->orderBy('total', 'desc')
            ->limit(7)
            ->get()
            ->pluck('total', 'seller_name');

        // ── 8. Payment Methods ────────────────────────────────────────────
        $paymentMethods = DB::table('payment')
            ->join('order', 'payment.order_id', '=', 'order.order_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->select('payment.payment_method', DB::raw('COUNT(*) as total'))
            ->groupBy('payment.payment_method')
            ->get()
            ->pluck('total', 'payment_method');

        // ── 9. Most Expensive Items (top 5 by unit price) ─────────────────
        $mostExpensiveItems = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereRaw('NOT product.is_deleted')
            ->select('product.product_id', 'product.name', 'product.price')
            ->distinct('product.product_id')
            ->orderBy('product.price', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'price' => (float) $r->price]);

        // ── 10. Least Expensive Items (bottom 5 by unit price) ────────────
        $leastExpensiveItems = DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $buyerId)
            ->whereRaw('NOT order.is_deleted')
            ->whereRaw('NOT product.is_deleted')
            ->select('product.product_id', 'product.name', 'product.price')
            ->distinct('product.product_id')
            ->orderBy('product.price', 'asc')
            ->limit(5)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'price' => (float) $r->price]);

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
