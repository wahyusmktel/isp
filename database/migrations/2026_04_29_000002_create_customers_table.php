<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20);
            $table->text('address');
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('pppoe_user', 100)->nullable();
            $table->string('onu_id', 100)->nullable();
            $table->enum('status', ['aktif', 'suspend', 'terminate'])->default('aktif');
            $table->date('join_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
