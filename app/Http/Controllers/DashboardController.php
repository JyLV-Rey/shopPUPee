<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Seller;

class DashboardController extends Controller
{
    public function buyer(Buyer $buyer)
    {
        return view('dashboard.buyer', compact('buyer'));
    }

    public function seller(Seller $seller)
    {
        $buyer = $seller->buyer;

        return view('dashboard.seller', compact('buyer', 'seller'));
    }
}
