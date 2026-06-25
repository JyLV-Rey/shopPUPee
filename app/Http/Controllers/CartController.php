<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $buyer = Auth::user();

        $cartItems = CartItem::with(['product.images', 'product.seller'])
            ->where('buyer_id', $buyer->buyer_id)
            ->get();

        $grouped = $cartItems->groupBy(fn ($item) => $item->product->seller->seller_name);

        $total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        $cartItemIds = implode(',', $cartItems->pluck('cart_item_id')->toArray());

        return view('cart.index', compact('cartItems', 'grouped', 'total', 'cartItemIds'));
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart');
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return redirect()->route('cart');
    }
}
