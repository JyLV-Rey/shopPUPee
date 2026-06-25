<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Product::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $trendingIds = DB::table('order_item')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->whereRaw('NOT product.is_deleted')
            ->select('product.product_id', DB::raw('SUM(order_item.quantity) as total_ordered'))
            ->groupBy('product.product_id')
            ->orderBy('total_ordered', 'desc')
            ->limit(8)
            ->pluck('product_id');

        $trending = Product::with(['images', 'seller', 'reviews'])
            ->whereIn('product_id', $trendingIds)
            ->get()
            ->sortBy(fn($p) => array_search($p->product_id, $trendingIds->toArray()))
            ->take(4);

        $featured = Product::with(['images', 'seller', 'reviews'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        return view('home.index', compact('categories', 'trending', 'featured'));
    }
}
