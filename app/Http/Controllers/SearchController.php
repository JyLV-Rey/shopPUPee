<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        return view('search.index', [
            'searchTerm' => $request->query('searchTerm'),
            'searchCategory' => $request->query('searchCategory'),
            'sortBy' => $request->query('sortBy', 'name'),
            'isDescending' => $request->boolean('isDescending'),
            'maxPrice' => $request->query('maxPrice'),
            'searchStore' => $request->query('searchStore'),
        ]);
    }
}
