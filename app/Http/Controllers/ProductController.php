<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function view(Product $product)
    {
        $product->load(['images', 'seller', 'reviews.buyer', 'priceHistories']);

        // Price over time
        $priceHistory = $product->priceHistories
            ->sortBy('date_set')
            ->groupBy(fn($ph) => $ph->date_set ? \Carbon\Carbon::parse($ph->date_set)->format('Y-m-d') : 'Unknown')
            ->map(fn($items) => $items->last()->price)
            ->toArray();

        // Units sold per month
        $unitsSold = \Illuminate\Support\Facades\DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->where('order_item.product_id', $product->product_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select(
                \Illuminate\Support\Facades\DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM') as month"),
                \Illuminate\Support\Facades\DB::raw('SUM(order_item.quantity) as total')
            )
            ->groupBy(\Illuminate\Support\Facades\DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Earnings per month
        $earnings = \Illuminate\Support\Facades\DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->where('order_item.product_id', $product->product_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select(
                \Illuminate\Support\Facades\DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM') as month"),
                \Illuminate\Support\Facades\DB::raw('SUM(order_item.quantity * product.price) as total')
            )
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->groupBy(\Illuminate\Support\Facades\DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        return view('product.view', compact('product', 'priceHistory', 'unitsSold', 'earnings'));
    }

    public function create()
    {
        $seller = Auth::user()->seller;

        if (! $seller) {
            return redirect()->route('home')->with('error', 'You need an approved seller account to create products.');
        }

        $categories = Product::select('category')->distinct()->pluck('category');

        return view('product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $seller = Auth::user()->seller;

        if (! $seller) {
            return redirect()->route('home')->with('error', 'You need an approved seller account to create products.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image_url' => 'nullable|url'
        ]);

        $product = Product::create([
            'seller_id' => $seller->seller_id,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'quantity' => $validatedData['quantity'],
            'category' => $validatedData['category'],
        ]);

        if (!empty($validatedData['image_url'])) {
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_url' => $validatedData['image_url'],
            ]);
        }

        return redirect()->route('home')->with('success', 'Product published successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Product::select('category')->distinct()->pluck('category');

        return view('product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image_url' => 'nullable|url'
        ]);

        $product->update([
            'seller_id' => Auth::user()->seller->seller_id,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'quantity' => $validatedData['quantity'],
            'category' => $validatedData['category'],
        ]);

        return redirect()->route('product.view', $product)->with('success', 'Product Updated Successfully!');
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('product_id', $product->product_id)
            ->where('buyer_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } else {
            Review::create([
                'product_id' => $product->product_id,
                'buyer_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        }

        return redirect()->route('product.view', $product)
            ->with('success', $existing ? 'Review updated!' : 'Review submitted!');
    }
}
