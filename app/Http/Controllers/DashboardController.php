<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function buyer(Buyer $buyer)
    {
        return view('dashboard.buyer', [
            'buyer'                 => $buyer,
            'totalSpent'            => $buyer->totalSpent(),
            'totalOrdersPlaced'     => $buyer->totalOrdersPlaced(),
            'totalCancelledOrders'  => $buyer->totalCancelledOrders(),
            'totalRefunds'          => $buyer->totalRefunds(),
            'topCategories'         => collect($buyer->topCategories()),
            'spendingOverTime'      => collect($buyer->spendingOverTime()),
            'spendByCategory'       => collect($buyer->spendByCategory()),
            'purchaseFrequency'     => collect($buyer->purchaseFrequency()),
            'topProducts'           => collect($buyer->topProducts()),
            'reviewRatings'         => collect($buyer->reviewRatings()),
            'preferredSellers'      => collect($buyer->preferredSellers()),
            'paymentMethods'        => collect($buyer->paymentMethods()),
            'mostExpensiveItems'    => collect($buyer->mostExpensiveItems()),
            'leastExpensiveItems'   => collect($buyer->leastExpensiveItems()),
        ]);

    }

    public function seller(Seller $seller)
    {
        // Structured arrays from model need flattening for chart views
        $topSellingProducts = collect($seller->topSellingProducts());
        $topReviewedProducts = collect($seller->topReviewedProducts());
        $mostExpensive = collect($seller->mostExpensiveProducts());
        $leastExpensive = collect($seller->leastExpensiveProducts());

        return view('dashboard.seller', [
            'seller'                    => $seller,
            'buyer'                     => $seller->buyer,
            'totalProductsListed'       => $seller->totalProductsListed(),
            'totalItemsSold'            => $seller->totalItemsSold(),
            'totalRevenue'              => $seller->totalRevenue(),
            'totalCancelled'            => $seller->totalCancelled(),
            'totalRefunded'             => $seller->totalRefunded(),
            'averageRating'             => $seller->averageRating(),
            'topSellingProducts'        => $topSellingProducts->mapWithKeys(fn($i) => [$i['name'] => $i['quantity']]),
            'topCategories'             => collect($seller->topCategories()),
            'monthlyEarnings'           => collect($seller->monthlyEarnings()),
            'purchaseFrequency'         => collect($seller->purchaseFrequency()),
            'topReviewedProducts'       => $topReviewedProducts->mapWithKeys(fn($i) => [$i['name'] => $i['count']]),
            'earningsByCategory'        => collect($seller->earningsByCategory()),
            'topBuyers'                 => collect($seller->topBuyers()),
            'mostExpensiveProducts'     => $mostExpensive->mapWithKeys(fn($i) => [$i['name'] => $i['price']]),
            'leastExpensiveProducts'    => $leastExpensive->mapWithKeys(fn($i) => [$i['name'] => $i['price']]),
            'orderStatusDist'           => collect($seller->orderStatusDistribution()),
            'lowStockProducts'          => $seller->lowStockProducts(),
        ]);
    }

    public function sellerOrders(Seller $seller, Request $request)
    {
        $sort = $request->query('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $status = $request->query('status');

        $query = Order::with(['buyer', 'items.product', 'delivery', 'payment'])
            ->whereHas('items.product', fn($q) => $q->where('seller_id', $seller->seller_id));

        if ($status) {
            $query->whereRaw('status::text = ?', [$status]);
        }

        $orders = $query->orderBy('ordered_at', $sort)->paginate(15);

        return view('dashboard.seller_orders', compact('seller', 'orders'));
    }

    public function updateDeliveryStatus(Request $request, Delivery $delivery)
    {
        $request->validate(['delivery_status' => 'required|string']);

        $delivery->update(['delivery_status' => $request->delivery_status]);

        return back()->with('success', "Delivery status updated to {$request->delivery_status}.");
    }
}
