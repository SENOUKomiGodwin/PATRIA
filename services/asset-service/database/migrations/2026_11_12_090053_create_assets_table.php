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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->date('acquisition_date');
            $table->decimal('acquisition_value', 15, 2);
            $table->enum('status', ['EN_SERVICE', 'EN_MAINTENANCE', 'REFORME', 'CEDE'])->default('EN_SERVICE');
            $table->enum('condition', ['NEUF', 'BON', 'MOYEN', 'MAUVAIS'])->default('NEUF');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service_id')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
