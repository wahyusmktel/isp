<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppBotController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $expectedToken = config('services.whatsapp.command_token');
        $givenToken = (string) $request->input('token', $request->header('X-Command-Token', ''));

        if ($expectedToken && ! hash_equals($expectedToken, $givenToken)) {
            return response()->json(['success' => false, 'message' => 'Invalid command token.'], 403);
        }

        $validated = $request->validate([
            'group_id' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'sender' => ['nullable', 'string', 'max:120'],
        ]);

        $configuredGroupId = Setting::get('whatsapp_notification_group_id', '');
        if (! $configuredGroupId || $validated['group_id'] !== $configuredGroupId) {
            return response()->json([
                'success' => true,
                'reply' => null,
                'message' => 'Command ignored: group is not configured.',
            ]);
        }

        $command = strtolower(trim($validated['message']));

        if ($command === '/client-online') {
            $totalOnline = (int) Router::sum('pppoe_online');
            $routerCount = Router::count();
            $onlineRouters = Router::where('status', 'online')->count();

            return response()->json([
                'success' => true,
                'reply' => implode("\n", [
                    '*Info Client PPPoE Online*',
                    '',
                    'Total client online: ' . $totalOnline,
                    'Router online: ' . $onlineRouters . '/' . $routerCount,
                    'Waktu: ' . now()->timezone(config('app.timezone'))->format('d/m/Y H:i:s'),
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'reply' => null,
            'message' => 'Command ignored.',
        ]);
    }
}
