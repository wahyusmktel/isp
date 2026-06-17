<?php

namespace App\Http\Middleware;

use App\Http\Controllers\IsolationController;
use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowIsolationPortalForIsolatedCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        $customer = Customer::where('ip_address', $request->ip())
            ->where('is_isolated', true)
            ->first();

        if ($customer) {
            return app(IsolationController::class)->portal($request);
        }

        return $next($request);
    }

    private function shouldBypass(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return true;
        }

        return $request->is(
            'isolir-portal*',
            'login*',
            'customer/login*',
            'build*',
            'storage*',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml'
        );
    }
}
