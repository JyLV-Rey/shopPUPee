<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
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

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:product,product_id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->quantity !== null && $request->quantity > $product->quantity) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $existing = CartItem::where('buyer_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $request->quantity;
            if ($product->quantity !== null && $newQty > $product->quantity) {
                return back()->with('error', 'Total quantity in cart exceeds available stock.');
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'buyer_id'   => Auth::id(),
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item added to cart!');
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return redirect()->route('cart');
    }
}
