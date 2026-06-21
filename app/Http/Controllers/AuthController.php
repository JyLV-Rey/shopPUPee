<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // TODO: handle login POST
    }

    public function showCreateAccount()
    {
        return view('account.create');
    }

    public function createAccount(Request $request)
    {
        // TODO: handle registration POST
    }

    public function showCreateSeller()
    {
        return view('account.create_seller');
    }

    public function createSeller(Request $request)
    {
        // TODO: handle seller application POST
    }

    public function logout(Request $request)
    {
        // TODO: handle logout
    }
}
