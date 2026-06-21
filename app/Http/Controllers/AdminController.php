<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function buyers()
    {
        return view('admin.buyers');
    }

    public function sellers()
    {
        return view('admin.sellers');
    }

    public function orders()
    {
        return view('admin.orders');
    }

    public function applications()
    {
        return view('admin.applications');
    }

    public function products()
    {
        return view('admin.products');
    }
}
