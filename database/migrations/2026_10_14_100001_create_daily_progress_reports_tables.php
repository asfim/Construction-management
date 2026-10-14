<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('dpr_number', 50);
            $table->date('report_date');
            $table->string('weather', 50)->default('Sunny');
            $table->foreignId('site_engineer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('skilled_workers')->default(0);
            $table->unsignedInteger('unskilled_workers')->default(0);
            $table->unsignedInteger('total_workers')->default(0);
            $table->text('work_performed');
            $table->text('equipment_deployed')->nullable();
            $table->text('material_received_used')->nullable();
            $table->text('issues_safety_notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'dpr_number']);
            $table->index(['company_id', 'project_id', 'report_date']);
        });

        Schema::create('dpr_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_progress_report_id')->constrained('daily_progress_reports')->cascadeOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->nullOnDelete();
            $table->string('activity_name');
            $table->string('location')->nullable();
            $table->decimal('quantity_completed', 18, 4)->default(0);
            $table->string('unit', 20);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpr_activities');
        Schema::dropIfExists('daily_progress_reports');
    }
};
