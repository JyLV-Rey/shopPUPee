<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ── List views ───────────────────────────────────────────────────────

    public function buyers()
    {
        $buyers = Buyer::withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.buyers', compact('buyers'));
    }

    public function sellers()
    {
        $sellers = Seller::with('buyer')
            ->withCount('products')
            ->orderBy('seller_id', 'desc')
            ->paginate(20);

        return view('admin.sellers', compact('sellers'));
    }

    public function orders()
    {
        $orders = Order::with(['buyer', 'items.product'])
            ->orderBy('ordered_at', 'desc')
            ->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function applications()
    {
        $applications = SellerApplication::with('buyer', 'address')
            ->orderBy('application_date', 'desc')
            ->paginate(20);

        return view('admin.applications', compact('applications'));
    }

    public function products()
    {
        $products = Product::with('seller')
            ->orderBy('product_id', 'desc')
            ->paginate(20);

        return view('admin.products', compact('products'));
    }

    // ── Toggle actions ───────────────────────────────────────────────────

    public function toggleBuyer(Buyer $buyer)
    {
        $buyer->update(['is_deleted' => ! $buyer->is_deleted]);

        return back()->with('success', $buyer->is_deleted
            ? 'Buyer account disabled.'
            : 'Buyer account restored.');
    }

    public function toggleSeller(Seller $seller)
    {
        $seller->update(['is_deleted' => ! $seller->is_deleted]);

        return back()->with('success', $seller->is_deleted
            ? 'Seller disabled.'
            : 'Seller restored.');
    }

    public function toggleProduct(Product $product)
    {
        $product->update(['is_deleted' => ! $product->is_deleted]);

        return back()->with('success', $product->is_deleted
            ? 'Product disabled.'
            : 'Product restored.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|string']);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order status updated to {$request->status}.");
    }

    // ── Application approve / reject ─────────────────────────────────────

    public function approveApplication(SellerApplication $application)
    {
        if ($application->status !== 'Pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'Approved']);

            Seller::create([
                'buyer_id'       => $application->buyer_id,
                'seller_name'    => $application->seller_name,
                'address_id'     => $application->address_id,
                'application_id' => $application->application_id,
            ]);
        });

        return back()->with('success', 'Seller application approved. Seller account created.');
    }

    public function rejectApplication(SellerApplication $application)
    {
        if ($application->status !== 'Pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        $application->update(['status' => 'Rejected']);

        return back()->with('success', 'Seller application rejected.');
    }
}
