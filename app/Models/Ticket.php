<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number', 'customer_id', 'category', 'priority',
        'subject', 'description', 'status', 'admin_notes',
        'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT-' . now()->format('Ymd') . '-';
        $last   = static::where('ticket_number', 'like', $prefix . '%')
                         ->orderByDesc('ticket_number')
                         ->value('ticket_number');
        $seq = $last ? (int) substr($last, -3) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'gangguan_jaringan' => 'Gangguan Jaringan',
            'lambat'            => 'Internet Lambat',
            'tidak_bisa_akses'  => 'Tidak Bisa Akses',
            'billing'           => 'Masalah Tagihan',
            default             => 'Lainnya',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'kritis' => 'Kritis',
            default  => $this->priority ?? '',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'Dibuka',
            'in_progress' => 'Diproses',
            'resolved'    => 'Selesai',
            'closed'      => 'Ditutup',
            default       => $this->status ?? '',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id'              => $this->id,
            'ticket_number'   => $this->ticket_number,
            'customer_id'     => $this->customer_id,
            'customer_name'   => $this->customer?->name,
            'customer_number' => $this->customer?->customer_number,
            'category'        => $this->category,
            'category_label'  => $this->category_label,
            'priority'        => $this->priority,
            'priority_label'  => $this->priority_label,
            'subject'         => $this->subject,
            'description'     => $this->description,
            'status'          => $this->status,
            'status_label'    => $this->status_label,
            'admin_notes'     => $this->admin_notes ?? '',
            'resolved_at'     => $this->resolved_at?->format('d M Y H:i'),
            'closed_at'       => $this->closed_at?->format('d M Y H:i'),
            'created_at'      => $this->created_at->format('d M Y H:i'),
        ];
    }
}
