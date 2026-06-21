<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function view(Request $request)
    {
        return view('product.view', [
            'productId' => $request->query('productId'),
        ]);
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        // TODO: handle product creation POST
    }

    public function edit(Request $request)
    {
        return view('product.edit', [
            'productId' => $request->query('productId'),
        ]);
    }

    public function update(Request $request)
    {
        // TODO: handle product update POST
    }
}
