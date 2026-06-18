<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odcs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('capacity')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('odps', function (Blueprint $table) {
            $table->foreignId('odc_id')->nullable()->after('router_id')->constrained('odcs')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable()->after('location');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('odp_id')->nullable()->after('package_id')->constrained('odps')->nullOnDelete();
            $table->unsignedInteger('cable_distance_meters')->nullable()->after('longitude');
            $table->timestamp('odp_mapped_at')->nullable()->after('cable_distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['odp_id']);
            $table->dropColumn(['odp_id', 'cable_distance_meters', 'odp_mapped_at']);
        });

        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['odc_id']);
            $table->dropColumn(['odc_id', 'latitude', 'longitude']);
        });

        Schema::dropIfExists('odcs');
    }
};
