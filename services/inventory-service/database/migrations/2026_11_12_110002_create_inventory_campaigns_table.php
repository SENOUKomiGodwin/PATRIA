<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['OUVERTE', 'CLOTUREE'])->default('OUVERTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_campaigns');
    }
};
