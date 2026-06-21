<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_deleted', false); // Make sure product is not deleted!

        // Each subsequent condition is only applied if the corresponding query parameter is present, allowing for flexible search combinations.
        // It is just a concatination of where clauses if the query parameters are present, and the final get() method executes the query and retrieves the results.
        // This is exactly like how react does it lol

        // Search term — match against name, description, or category
        if ($searchTerm = $request->query('searchTerm')) {

            /// extra function to properly chain the OR conditions without affecting the rest of the query
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($category = $request->query('searchCategory')) {
            $query->where('category', $category);
        }

        // Price ceiling
        if ($maxPrice = $request->query('maxPrice')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // Store filter — search via the seller relation
        if ($searchStore = $request->query('searchStore')) {
            $query->whereHas('seller', function ($q) use ($searchStore) {
                $q->where('seller_name', 'like', "%{$searchStore}%")
                  ->where('is_deleted', false);
            });
        }

        // Sorting
        $sortBy = $request->query('sortBy', 'name');
        $direction = $request->boolean('isDescending') ? 'desc' : 'asc';

        $allowedSorts = ['name', 'price', 'created_at', 'quantity'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }

        // Finally execute the query, hinted by the get() method
        $products = $query->with(['images', 'seller'])->orderBy($sortBy, $direction)->get();

        $categories = Product::where('is_deleted', false)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('search.index', [
            'products' => $products,
            'categories' => $categories,
            'searchTerm' => $request->query('searchTerm'),
            'searchCategory' => $request->query('searchCategory'),
            'sortBy' => $request->query('sortBy', 'name'),
            'isDescending' => $request->boolean('isDescending'),
            'maxPrice' => $request->query('maxPrice'),
            'searchStore' => $request->query('searchStore'),
        ]);
    }
}
