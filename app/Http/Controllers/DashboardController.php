<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function buyer(Request $request)
    {
        return view('dashboard.buyer', [
            'buyerId' => $request->query('buyerId'),
        ]);
    }

    public function seller(Request $request)
    {
        return view('dashboard.seller', [
            'sellerId' => $request->query('sellerId'),
        ]);
    }
}
