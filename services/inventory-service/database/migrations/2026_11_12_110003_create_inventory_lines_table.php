<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained('inventory_campaigns')->cascadeOnDelete();
            $table->uuid('asset_id')->index();
            $table->unsignedBigInteger('expected_location_id')->nullable();
            $table->unsignedBigInteger('found_location_id')->nullable();
            $table->enum('result', ['CONFORME', 'ECART', 'MANQUANT']);
            $table->foreignId('counted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_lines');
    }
};
