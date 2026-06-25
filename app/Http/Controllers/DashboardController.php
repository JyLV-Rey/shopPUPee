<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Seller;

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
}
