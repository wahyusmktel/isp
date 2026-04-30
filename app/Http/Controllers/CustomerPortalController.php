<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;

class CustomerPortalController extends Controller
{
    public function dashboard()
    {
        $customerId = Session::get('customer_id');
        if (!$customerId) {
            return redirect()->route('customer.login');
        }

        $customer = Customer::find($customerId);
        if (!$customer) {
            Session::forget('customer_id');
            return redirect()->route('customer.login');
        }

        return view('customer.dashboard', compact('customer'));
    }
}
