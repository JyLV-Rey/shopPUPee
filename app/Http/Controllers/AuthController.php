<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Address;
use App\Models\SellerApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view('account.login', [
            'redirect' => $request->boolean('redirect'),
            'accountCreated' => $request->boolean('accountCreated'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $buyer = Buyer::where('email', $request->email)->first();

        if (!$buyer || $buyer->password !== $request->password) {
            return back()->with('error', 'Invalid Credentials')->withInput($request->only('email'));
        }

        if ($buyer->is_deleted) {
            return back()->with('error', 'Account deactivated');
        }

        Auth::login($buyer);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function showCreateAccount()
    {
        return view('account.create');
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:buyer,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:4'
        ]);

        Buyer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);

        return redirect()->route('account.login', ['accountCreated' => true]);
    }

    public function showCreateSeller()
    {
        return view('account.create_seller');
    }

    public function createSeller(Request $request)
    {
        $request->validate([
            'seller_name' => 'required|string|max:255',
            'valid_id_url' => 'required|url',
            'street' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'province' => 'required|string',
            'barangay' => 'required|string',
            'region' => 'required|string',
            'unit_floor' => 'nullable|string',
            'additional_notes' => 'nullable|string'
        ]);

        $buyerId = Auth::id();

        DB::transaction(function () use ($request, $buyerId) {
            $address = Address::create([
                'buyer_id' => $buyerId,
                'street' => $request->street,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'province' => $request->province,
                'barangay' => $request->barangay,
                'region' => $request->region,
                'unit_floor' => $request->unit_floor,
                'additional_notes' => $request->additional_notes,
            ]);

            SellerApplication::create([
                'buyer_id' => $buyerId,
                'seller_name' => $request->seller_name,
                'valid_id_url' => $request->valid_id_url,
                'address_id' => $address->address_id,
                'status' => 'Pending',
            ]);
        });

        return redirect()->route('home')->with('success', 'Seller application submitted successfully! Please wait for admin approval.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}
