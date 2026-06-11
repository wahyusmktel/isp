<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileCustomerController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_number' => ['required', 'string', 'max:50'],
        ]);

        $customer = Customer::with('package')
            ->where('customer_number', $validated['customer_number'])
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pelanggan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'customer' => $this->customerData($customer),
            'invoices' => $this->invoiceQuery($customer)->take(10)->get()->map(fn (Invoice $invoice) => $this->invoiceData($invoice))->values(),
            'summary' => $this->invoiceSummary($customer),
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_number' => ['required', 'string', 'max:50'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $customer = Customer::with('package')
            ->where('customer_number', $validated['customer_number'])
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pelanggan tidak ditemukan.',
            ], 404);
        }

        $limit = (int) ($validated['limit'] ?? 25);

        return response()->json([
            'success' => true,
            'customer' => $this->customerData($customer),
            'summary' => $this->invoiceSummary($customer),
            'invoices' => $this->invoiceQuery($customer)->take($limit)->get()->map(fn (Invoice $invoice) => $this->invoiceData($invoice))->values(),
        ]);
    }

    public function news(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $limit = (int) ($validated['limit'] ?? 5);

        $news = News::query()
            ->where('status', 'published')
            ->latest('published_at')
            ->take($limit)
            ->get()
            ->map(fn (News $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt ?: Str::limit(strip_tags($item->body), 120),
                'category' => $item->category,
                'category_label' => $item->category_label,
                'published_at' => $item->published_at?->format('Y-m-d H:i:s'),
                'published_at_label' => $item->published_at?->translatedFormat('d F Y') ?? '-',
            ])
            ->values();

        return response()->json([
            'success' => true,
            'news' => $news,
        ]);
    }

    private function invoiceQuery(Customer $customer)
    {
        return Invoice::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('billing_period')
            ->orderByDesc('created_at');
    }

    private function invoiceSummary(Customer $customer): array
    {
        $invoices = Invoice::where('customer_id', $customer->id)->get();

        return [
            'total' => $invoices->count(),
            'unpaid' => $invoices->where('status', 'unpaid')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'outstanding_amount' => (int) $invoices->whereIn('status', ['unpaid', 'overdue'])->sum('amount'),
        ];
    }

    private function customerData(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_number' => $customer->customer_number,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'status' => $customer->status,
            'status_label' => $customer->status_label,
            'package_name' => $customer->package?->name ?? 'Belum ada paket',
            'package_price' => (int) ($customer->package?->price ?? 0),
            'billing_date' => $customer->billing_date ?? 1,
        ];
    }

    private function invoiceData(Invoice $invoice): array
    {
        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'billing_period' => $invoice->billing_period?->format('Y-m-d'),
            'billing_period_label' => $invoice->billing_period?->translatedFormat('F Y') ?? '-',
            'amount' => (int) $invoice->amount,
            'status' => $invoice->status,
            'status_label' => $invoice->status_label,
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'due_date_label' => $invoice->due_date?->translatedFormat('d F Y') ?? '-',
            'paid_at' => $invoice->paid_at?->format('Y-m-d H:i:s'),
            'payment_method' => $invoice->payment_method,
            'notes' => $invoice->notes,
        ];
    }
}
