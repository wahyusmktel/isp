<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_configs', function (Blueprint $table) {
            $table->id();
            $table->string('jabatan')->unique();
            $table->bigInteger('base_salary')->default(0);
            $table->bigInteger('allowance')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_configs');
    }
};
