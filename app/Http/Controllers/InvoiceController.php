<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer:id,name,phone')
            ->orderByDesc('created_at')
            ->get();

        $customers = Customer::with('package:id,price')
            ->where('status', '!=', 'terminate')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'package_id']);

        $stats = [
            'total'     => $invoices->count(),
            'unpaid'    => $invoices->where('status', 'unpaid')->count(),
            'paid'      => $invoices->where('status', 'paid')->count(),
            'overdue'   => $invoices->where('status', 'overdue')->count(),
        ];

        return view('invoices.index', compact('invoices', 'customers', 'stats'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        if (empty($validated['invoice_number'])) {
            // Generate simple invoice number like INV-202604-0001
            $validated['invoice_number'] = 'INV-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        $invoice = Invoice::create($validated);
        $invoice->load('customer:id,name,phone');

        return response()->json([
            'success' => true,
            'message' => "Tagihan \"{$invoice->invoice_number}\" berhasil dibuat.",
            'invoice' => $invoice->toJsonData(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate($this->rules($invoice->id));
        
        $invoice->update($validated);
        $invoice->load('customer:id,name,phone');

        return response()->json([
            'success' => true,
            'message' => "Data tagihan \"{$invoice->invoice_number}\" berhasil diperbarui.",
            'invoice' => $invoice->toJsonData(),
        ]);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $number = $invoice->invoice_number;
        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => "Tagihan \"{$number}\" berhasil dihapus.",
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate(['status' => 'required|in:unpaid,paid,overdue,cancelled']);
        
        $updateData = ['status' => $request->status];
        if ($request->status === 'paid' && !$invoice->paid_at) {
            $updateData['paid_at'] = now();
        } elseif ($request->status !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $invoice->update($updateData);

        $labels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'overdue' => 'Jatuh Tempo',
            'cancelled' => 'Dibatalkan'
        ];

        return response()->json([
            'success' => true,
            'message' => "Status tagihan \"{$invoice->invoice_number}\" menjadi {$labels[$request->status]}.",
            'status'  => $invoice->status,
        ]);
    }

    public function updatePaymentMethod(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate(['payment_method' => 'nullable|string|max:50']);
        
        $invoice->update(['payment_method' => $request->payment_method]);

        return response()->json([
            'success' => true,
            'message' => "Metode pembayaran tagihan \"{$invoice->invoice_number}\" berhasil diperbarui.",
            'payment_method' => $invoice->payment_method,
        ]);
    }

    public function generateMass(Request $request): JsonResponse
    {
        $currentDate = now();
        $period = $currentDate->startOfMonth();
        $dueDate = $currentDate->copy()->addDays(7); // Due date is 7 days from now

        $customers = Customer::with('package')->where('status', 'aktif')->get();
        $count = 0;

        foreach ($customers as $customer) {
            if (!$customer->package) continue; // Skip if no package

            // Check if invoice already exists for this customer in the current month
            $exists = Invoice::where('customer_id', $customer->id)
                ->whereYear('billing_period', $period->year)
                ->whereMonth('billing_period', $period->month)
                ->exists();

            if (!$exists) {
                Invoice::create([
                    'invoice_number' => 'INV-' . $period->format('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'customer_id'    => $customer->id,
                    'billing_period' => $period->format('Y-m-d'),
                    'amount'         => $customer->package->price,
                    'status'         => 'unpaid',
                    'due_date'       => $dueDate->format('Y-m-d'),
                    'payment_method' => 'Transfer Bank', // Default
                    'notes'          => 'Tagihan otomatis periode ' . $period->format('F Y'),
                ]);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses tagihan massal. {$count} tagihan baru dibuat.",
        ]);
    }

    public function printPdf(Invoice $invoice)
    {
        $invoice->load('customer.package');
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        // Optional: set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function rules($id = null): array
    {
        return [
            'invoice_number' => 'nullable|string|max:50|unique:invoices,invoice_number' . ($id ? ',' . $id : ''),
            'customer_id'    => 'required|exists:customers,id',
            'billing_period' => 'required|date',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:unpaid,paid,overdue,cancelled',
            'due_date'       => 'required|date',
            'paid_at'        => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
