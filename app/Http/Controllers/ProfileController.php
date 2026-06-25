<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //  Buyer profile

    public function editBuyer(Request $request)
    {
        return view('account.edit_buyer');
    }

    public function updateBuyer(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:65',
            'last_name'  => 'required|string|max:65',
            'email'      => 'required|email|max:65|unique:buyer,email,' . Auth::id() . ',buyer_id',
            'phone'      => 'nullable|string|max:65',
        ]);

        $user = Auth::user();
        $user->update($validated);
        $user->refresh();

        return redirect()->route('edit.buyer')->with('success', 'Profile updated successfully.');
    }

    //  Seller profile

    public function editSeller(Request $request)
    {
        $seller = Auth::user()->seller;

        if (!$seller) {
            return redirect()->route('home');
        }

        return view('account.edit_seller', compact('seller'));
    }

    public function updateSeller(Request $request)
    {
        $validated = $request->validate([
            'seller_name' => 'required|string|max:255',
        ]);

        $seller = Auth::user()->seller;

        if (!$seller) {
            return redirect()->route('home');
        }

        $seller->update(['seller_name' => $validated['seller_name']]);

        return redirect()->route('dashboard.buyer', Auth::user());
    }

    public function editAddress(Request $request)
    {
        $address = Address::where('address_id', $request->query('addressId'))
            ->where('buyer_id', Auth::user()->buyer_id)
            ->firstOrFail();

        return view('address.edit', compact('address'));
    }

    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'street'           => 'required|string|max:65',
            'city'             => 'required|string|max:65',
            'province'         => 'required|string|max:65',
            'barangay'         => 'required|string|max:65',
            'region'           => 'required|string|max:65',
            'postal_code'      => 'nullable|string|max:10',
            'unit_floor'       => 'nullable|string|max:65',
            'additional_notes' => 'nullable|string|max:255',
        ]);

        $address = Address::where('address_id', $request->query('addressId'))
            ->where('buyer_id', Auth::user()->buyer_id)
            ->firstOrFail();

        $address->update($validated);

        return redirect()->route('edit.address', ['addressId' => $address->address_id]);
    }

    public function addAddress()
    {
        return view('address.add');
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'street'           => 'required|string|max:65',
            'city'             => 'required|string|max:65',
            'province'         => 'required|string|max:65',
            'barangay'         => 'required|string|max:65',
            'region'           => 'required|string|max:65',
            'postal_code'      => 'nullable|string|max:10',
            'unit_floor'       => 'nullable|string|max:65',
            'additional_notes' => 'nullable|string|max:255',
        ]);

        Address::create(array_merge($validated, [
            'buyer_id' => Auth::user()->buyer_id,
        ]));

        return redirect()->route('address.add');
    }
}
