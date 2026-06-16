<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function index()
    {
        return view('whatsapp.index', [
            'bridgeUrl' => config('services.whatsapp.bridge_url'),
        ]);
    }

    public function connect(): JsonResponse
    {
        return $this->bridgePost('/connect');
    }

    public function status(): JsonResponse
    {
        return $this->bridgeGet('/status');
    }

    public function logout(): JsonResponse
    {
        return $this->bridgePost('/logout');
    }

    public function reset(): JsonResponse
    {
        return $this->bridgePost('/reset');
    }

    public function sendTest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return $this->bridgePost('/send', $validated);
    }

    private function bridgeGet(string $path): JsonResponse
    {
        try {
            $response = Http::timeout($this->timeout())->acceptJson()->get($this->bridgeUrl($path));

            return response()->json($response->json() ?? [
                'success' => false,
                'message' => 'Respons WhatsApp bridge tidak valid.',
            ], $response->status());
        } catch (ConnectionException) {
            return $this->offlineResponse();
        }
    }

    private function bridgePost(string $path, array $payload = []): JsonResponse
    {
        try {
            $response = Http::timeout($this->timeout())->acceptJson()->post($this->bridgeUrl($path), $payload);

            return response()->json($response->json() ?? [
                'success' => false,
                'message' => 'Respons WhatsApp bridge tidak valid.',
            ], $response->status());
        } catch (ConnectionException) {
            return $this->offlineResponse();
        } catch (RequestException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    private function offlineResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => 'offline',
            'connected' => false,
            'message' => 'WhatsApp bridge belum berjalan. Jalankan npm run wa:start di server.',
        ], 503);
    }

    private function bridgeUrl(string $path): string
    {
        return rtrim((string) config('services.whatsapp.bridge_url'), '/') . $path;
    }

    private function timeout(): int
    {
        return (int) config('services.whatsapp.timeout', 10);
    }
}
