<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CustomerActivityController extends Controller
{
    public function index()
    {
        $recentWindowHours = 24;
        $recentSince = now()->subHours($recentWindowHours);

        $activities = CustomerActivity::with('customer')
            ->orderByDesc('created_at')
            ->paginate(20);

        $recentActivities = CustomerActivity::with('customer')
            ->where('created_at', '>=', $recentSince)
            ->orderBy('created_at')
            ->get();

        $recentDisconnects = $recentActivities
            ->filter(fn (CustomerActivity $activity) => $this->isDisconnectAction($activity->action));

        $topDisconnectCustomers = $recentDisconnects
            ->groupBy(fn (CustomerActivity $activity) => $this->activityCustomerKey($activity))
            ->map(function (Collection $items) {
                $latest = $items->sortByDesc('created_at')->first();

                return [
                    'customer' => $latest->customer,
                    'pppoe_user' => $latest->pppoe_user,
                    'disconnect_count' => $items->count(),
                    'last_seen' => $latest->created_at,
                ];
            })
            ->sortByDesc('disconnect_count')
            ->take(5)
            ->values();

        $avgDisconnectDurationSeconds = $this->averageDisconnectDurationSeconds($recentActivities);

        $disconnectSummary = [
            'window_hours' => $recentWindowHours,
            'total_disconnects' => $recentDisconnects->count(),
            'affected_customers' => $recentDisconnects
                ->map(fn (CustomerActivity $activity) => $this->activityCustomerKey($activity))
                ->unique()
                ->count(),
            'avg_duration' => $avgDisconnectDurationSeconds !== null
                ? $this->formatDuration($avgDisconnectDurationSeconds)
                : null,
            'paired_events' => $this->countPairedDisconnectEvents($recentActivities),
        ];

        return view('customers.activities', compact(
            'activities',
            'topDisconnectCustomers',
            'disconnectSummary'
        ));
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

        if (! $user || ! $action) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $customer = Customer::where('pppoe_user', $user)->first();

        $activity = CustomerActivity::create([
            'customer_id' => $customer ? $customer->id : null,
            'pppoe_user' => $user,
            'action' => $action,
            'ip_address' => $ip,
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
            'logged in',
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
            'logged out',
        ], true);
    }

    private function activityCustomerKey(CustomerActivity $activity): string
    {
        return $activity->customer_id
            ? 'customer:'.$activity->customer_id
            : 'pppoe:'.($activity->pppoe_user ?: 'unknown');
    }

    private function averageDisconnectDurationSeconds(Collection $activities): ?int
    {
        $durations = [];

        $activities
            ->groupBy(fn (CustomerActivity $activity) => $this->activityCustomerKey($activity))
            ->each(function (Collection $items) use (&$durations) {
                $openDisconnectAt = null;

                foreach ($items->sortBy('created_at') as $activity) {
                    if ($this->isDisconnectAction($activity->action)) {
                        $openDisconnectAt = $activity->created_at;

                        continue;
                    }

                    if ($openDisconnectAt && $this->isConnectAction($activity->action)) {
                        $durations[] = $openDisconnectAt->diffInSeconds($activity->created_at);
                        $openDisconnectAt = null;
                    }
                }
            });

        if (count($durations) === 0) {
            return null;
        }

        return (int) round(array_sum($durations) / count($durations));
    }

    private function countPairedDisconnectEvents(Collection $activities): int
    {
        $pairedEvents = 0;

        $activities
            ->groupBy(fn (CustomerActivity $activity) => $this->activityCustomerKey($activity))
            ->each(function (Collection $items) use (&$pairedEvents) {
                $openDisconnectAt = null;

                foreach ($items->sortBy('created_at') as $activity) {
                    if ($this->isDisconnectAction($activity->action)) {
                        $openDisconnectAt = $activity->created_at;

                        continue;
                    }

                    if ($openDisconnectAt && $this->isConnectAction($activity->action)) {
                        $pairedEvents++;
                        $openDisconnectAt = null;
                    }
                }
            });

        return $pairedEvents;
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds.' detik';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $minutes.' menit'.($remainingSeconds ? ' '.$remainingSeconds.' detik' : '');
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $hours.' jam'.($remainingMinutes ? ' '.$remainingMinutes.' menit' : '');
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
            'unreadCount' => $unreadCount,
        ]);
    }
}
