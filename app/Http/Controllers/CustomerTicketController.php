<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerTicketController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 401);
        }

        $validated = $request->validate([
            'category'    => 'required|in:gangguan_jaringan,lambat,tidak_bisa_akses,billing,lainnya',
            'priority'    => 'required|in:rendah,sedang,tinggi,kritis',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        $validated['customer_id']   = $customerId;
        $validated['ticket_number'] = Ticket::generateTicketNumber();

        $ticket = Ticket::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Tiket #{$ticket->ticket_number} berhasil dibuat. Tim kami akan segera menindaklanjuti.",
            'ticket'  => [
                'ticket_number'  => $ticket->ticket_number,
                'subject'        => $ticket->subject,
                'category_label' => $ticket->category_label,
                'priority'       => $ticket->priority,
                'status'         => $ticket->status,
                'status_label'   => $ticket->status_label,
                'admin_notes'    => '',
                'created_at'     => $ticket->created_at->format('d M Y H:i'),
            ],
        ]);
    }
}
