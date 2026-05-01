<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->get('status', '');
        $category = $request->get('category', '');
        $search   = $request->get('search', '');

        $query = Ticket::with('customer:id,name,customer_number')->latest();

        if ($status)   $query->where('status', $status);
        if ($category) $query->where('category', $category);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'open'        => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved'    => Ticket::where('status', 'resolved')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
        ];

        return view('tickets.index', compact('tickets', 'stats', 'status', 'category', 'search'));
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }
        if ($validated['status'] === 'closed' && $ticket->status !== 'closed') {
            $validated['closed_at'] = now();
        }

        $ticket->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Tiket #{$ticket->ticket_number} berhasil diperbarui.",
            'ticket'  => $ticket->fresh('customer')->toJsonData(),
        ]);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $num = $ticket->ticket_number;
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => "Tiket #{$num} berhasil dihapus.",
        ]);
    }
}
