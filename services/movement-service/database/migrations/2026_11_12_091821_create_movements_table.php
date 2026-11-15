<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id')->index();
            $table->enum('type', ['AFFECTATION', 'TRANSFERT', 'CESSION', 'REFORME']);
            $table->string('from_service_id')->nullable();
            $table->string('to_service_id')->nullable();
            $table->unsignedBigInteger('to_location_id')->nullable();
            $table->date('movement_date');
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
