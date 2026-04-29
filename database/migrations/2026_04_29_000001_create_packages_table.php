<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 30)->default('home'); // home | bisnis | dedicated
            $table->unsignedSmallInteger('speed_download');  // Mbps
            $table->unsignedSmallInteger('speed_upload');    // Mbps
            $table->decimal('price', 12, 2);
            $table->string('contention', 20)->nullable();    // e.g. "1:8"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
