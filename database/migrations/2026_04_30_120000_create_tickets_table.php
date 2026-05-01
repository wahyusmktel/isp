<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['gangguan_jaringan', 'lambat', 'tidak_bisa_akses', 'billing', 'lainnya'])->default('lainnya');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
