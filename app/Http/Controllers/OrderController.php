<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * GET /orders
     * List all orders for the authenticated buyer.
     */
    public function orders(Request $request)
    {
        $sort   = $request->query('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $status = $request->query('status');

        $query = Order::with(['items.product'])
            ->where('buyer_id', Auth::user()->buyer_id)
            ->orderBy('ordered_at', $sort);

        if ($status) {
            $query->whereRaw('status::text = ?', [$status]);
        }

        $orders = $query->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * GET /product/confirm_order
     * Show the order confirmation / checkout page.
     */
    public function confirmOrder(Request $request)
    {
        $cartItemIds = array_filter(explode(',', $request->query('cartItems', '')));

        if (empty($cartItemIds)) {
            return redirect()->route('cart')->with('error', 'No items selected for checkout.');
        }

        $cartItems = CartItem::with(['product.images', 'product.seller'])
            ->whereIn('cart_item_id', $cartItemIds)
            ->where('buyer_id', Auth::user()->buyer_id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Cart items not found.');
        }

        $total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        $addresses = Address::where('buyer_id', Auth::user()->buyer_id)->get();

        return view('product.confirm_order', compact('cartItems', 'total', 'addresses'));
    }

    /**
     * POST /product/confirm_order
     * Create the order, order items, payment, delivery; clear cart items.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'cart_item_ids'  => 'required|string',
            'address_id'     => 'required|integer',
            'payment_method' => 'nullable|string|max:50',
            'courier_service' => 'nullable|string|max:50',
        ]);

        // Verify the address belongs to the authenticated user
        $address = Address::where('address_id', $request->input('address_id'))
            ->where('buyer_id', Auth::id())
            ->first();

        if (! $address) {
            abort(403, 'The selected address does not belong to you.');
        }

        $cartItemIds = collect(explode(',', $request->input('cart_item_ids')))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        $newOrderId = null;

        try {
            DB::transaction(function () use ($cartItemIds, $request, $address, &$newOrderId) {
            $cartItems = CartItem::with('product')
                ->whereIn('cart_item_id', $cartItemIds)
                ->where('buyer_id', Auth::id())
                ->get();

            $order = Order::create([
                'buyer_id'   => Auth::user()->buyer_id,
                'status'     => 'Pending',
                'ordered_at' => now(),
            ]);

            $newOrderId = $order->order_id;

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                ]);

                // Decrement stock
                if ($item->product->quantity !== null) {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }
            Payment::create([
                'order_id'       => $order->order_id,
                'payment_method' => $request->input('payment_method', 'COD'),
                'payment_status' => 'Pending',
            ]);

            Delivery::create([
                'order_id'        => $order->order_id,
                'delivery_status' => 'Preparing',
                'courier_service' => $request->input('courier_service', 'Standard'),
                'buyer_address_id' => $request->input('address_id'),
            ]);

            // Clear the purchased cart items
            CartItem::whereIn('cart_item_id', $cartItems->pluck('cart_item_id'))->delete();
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart')->with('error', $e->getMessage());
        }

        return redirect()->route('product.receipt', ['orderId' => $newOrderId, 'justOrdered' => 'true']);
    }

    /**
     * GET /product/view_receipt
     * Show the receipt page for a given order.
     */
    public function viewReceipt(Request $request)
    {
        $orderId = $request->query('orderId');

        $order = Order::with([
            'items.product.images',
            'items.product.seller.address',
            'delivery.address',
            'payment',
            'buyer',
        ])
            ->where('order_id', $orderId)
            ->where('buyer_id', Auth::id())
            ->firstOrFail();

        $justOrdered = $request->boolean('justOrdered');

        return view('product.view_receipt', compact('order', 'justOrdered'));
    }

}
