<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('host', 255);
            $table->unsignedSmallInteger('api_port')->default(8728);
            $table->unsignedSmallInteger('winbox_port')->nullable();
            $table->string('username', 100)->default('admin');
            $table->string('password', 255);
            $table->string('location', 255)->nullable();
            $table->enum('status', ['online', 'offline', 'unknown'])->default('unknown');
            $table->string('model', 100)->nullable();
            $table->string('firmware', 50)->nullable();
            $table->unsignedInteger('pppoe_online')->default(0);
            $table->timestamp('last_check_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
