<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function editBuyer(Request $request)
    {
        return view('account.edit_buyer', [
            'buyerId' => $request->query('buyerId'),
        ]);
    }

    public function updateBuyer(Request $request)
    {
        // TODO: handle buyer profile update POST
    }

    public function editSeller(Request $request)
    {
        return view('account.edit_seller', [
            'sellerId' => $request->query('sellerId'),
        ]);
    }

    public function updateSeller(Request $request)
    {
        // TODO: handle seller profile update POST
    }

    public function editAddress(Request $request)
    {
        return view('address.edit', [
            'addressId' => $request->query('addressId'),
        ]);
    }

    public function updateAddress(Request $request)
    {
        // TODO: handle address update POST
    }

    public function addAddress()
    {
        return view('address.add');
    }

    public function storeAddress(Request $request)
    {
        // TODO: handle address creation POST
    }
}
