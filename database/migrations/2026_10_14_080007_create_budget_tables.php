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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boq_version_id')->nullable()->constrained('boq_versions')->nullOnDelete();
            $table->string('budget_code', 50);
            $table->string('title');
            $table->decimal('total_budget_amount', 18, 2)->default(0.00);
            $table->decimal('contingency_amount', 18, 2)->default(0.00);
            $table->string('status', 30)->default('draft'); // draft, approved, revised
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'budget_code']);
            $table->index(['company_id', 'project_id', 'status']);
        });

        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('cost_code_id')->constrained('cost_codes')->cascadeOnDelete();
            $table->decimal('planned_amount', 18, 2)->default(0.00);
            $table->decimal('committed_amount', 18, 2)->default(0.00); // From open POs
            $table->decimal('actual_amount', 18, 2)->default(0.00);    // Invoiced / Consumed
            $table->decimal('forecast_amount', 18, 2)->default(0.00);  // Forecast at completion
            $table->timestamps();

            $table->unique(['budget_id', 'cost_code_id']);
            $table->index('budget_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
    }
};
