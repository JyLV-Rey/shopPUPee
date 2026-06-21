<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function confirmOrder(Request $request)
    {
        return view('product.confirm_order', [
            'cartItems' => $request->query('cartItems'),
        ]);
    }

    public function viewReceipt(Request $request)
    {
        return view('product.view_receipt', [
            'orderId' => $request->query('orderId'),
            'justOrdered' => $request->boolean('justOrdered'),
        ]);
    }

    public function orders(Request $request)
    {
        return view('orders.index', [
            'buyerId' => $request->query('buyerId'),
        ]);
    }
}
