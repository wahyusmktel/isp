<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (Session::has('customer_id')) {
            return redirect()->route('customer.dashboard');
        }
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'customer_number' => 'required|string',
        ]);

        $customer = Customer::where('customer_number', $request->customer_number)->first();

        if ($customer) {
            Session::put('customer_id', $customer->id);
            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'customer_number' => 'Nomor Pelanggan tidak ditemukan.',
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('customer_id');
        return redirect()->route('customer.login');
    }
}
