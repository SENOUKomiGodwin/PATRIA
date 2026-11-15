<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_interventions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id')->index();
            $table->foreignUuid('plan_id')->nullable()->constrained('maintenance_plans')->nullOnDelete();
            $table->enum('type', ['PREVENTIVE', 'CORRECTIVE']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['PLANIFIEE', 'EN_COURS', 'TERMINEE', 'ANNULEE'])->default('PLANIFIEE');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('scheduled_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('provider')->nullable();
            $table->text('report')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_interventions');
    }
};
