<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function view(Product $product)
    {
        $product->load(['images', 'seller', 'reviews.buyer']);

        return view('product.view', compact('product'));
    }

    public function create()
    {
        $categories = Product::select('category')->distinct()->pluck('category');

        return view('product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // TODO: handle product creation POST
    }

    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // TODO: handle product update POST
    }
}
