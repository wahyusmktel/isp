<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Services\WhatsAppNotificationService;
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

    public function webhook(Request $request, WhatsAppNotificationService $whatsApp): JsonResponse
    {
        $expectedToken = config('services.customer_activity.webhook_token');
        $givenToken = (string) $request->input('token', $request->header('X-Webhook-Token', ''));

        if ($expectedToken && ! hash_equals($expectedToken, $givenToken)) {
            return response()->json(['error' => 'Invalid webhook token'], 403);
        }

        // Parameter expected from Mikrotik script: pppoe_user, action (connected/disconnected), ip_address, description
        $user = $request->input('pppoe_user');
        $action = $request->input('action'); // connected or disconnected
        $ip = $request->input('ip_address');
        $desc = $request->input('description');

        if (!$user || !$action) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $customer = Customer::where('pppoe_user', $user)->first();

        $activity = CustomerActivity::create([
            'customer_id' => $customer ? $customer->id : null,
            'pppoe_user'  => $user,
            'action'      => $action,
            'ip_address'  => $ip,
            'description' => $desc,
        ]);

        if ($this->isConnectAction($action)) {
            $whatsApp->notifyPppoeConnected($activity);
        } elseif ($this->isDisconnectAction($action)) {
            $whatsApp->notifyPppoeDisconnected($activity);
        }

        return response()->json(['success' => true]);
    }

    private function isConnectAction(string $action): bool
    {
        return in_array(strtolower(trim($action)), [
            'connected',
            'connect',
            'up',
            'online',
            'login',
            'logged_in',
        ], true);
    }

    private function isDisconnectAction(string $action): bool
    {
        return in_array(strtolower(trim($action)), [
            'disconnected',
            'disconnect',
            'down',
            'offline',
            'logout',
            'logged_out',
        ], true);
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
