<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerActivityController extends Controller
{
    public function index()
    {
        $activities = CustomerActivity::with('customer')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('customers.activities', compact('activities'));
    }

    public function webhook(Request $request): JsonResponse
    {
        // Parameter expected from Mikrotik script: pppoe_user, action (connected/disconnected), ip_address, description
        $user = $request->input('pppoe_user');
        $action = $request->input('action'); // connected or disconnected
        $ip = $request->input('ip_address');
        $desc = $request->input('description');

        if (!$user || !$action) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $customer = Customer::where('pppoe_user', $user)->first();

        CustomerActivity::create([
            'customer_id' => $customer ? $customer->id : null,
            'pppoe_user'  => $user,
            'action'      => $action,
            'ip_address'  => $ip,
            'description' => $desc,
        ]);

        return response()->json(['success' => true]);
    }

    public function latestApi(): JsonResponse
    {
        $activities = CustomerActivity::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
            
        $unreadCount = CustomerActivity::where('created_at', '>=', now()->subHours(24))->count();

        return response()->json([
            'activities' => $activities,
            'unreadCount' => $unreadCount
        ]);
    }
}
