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
        Schema::create('material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('requisition_no', 50); // e.g. MR-GLS-001
            $table->date('requisition_date');
            $table->date('required_by_date')->nullable();
            $table->string('status', 30)->default('draft'); // draft, submitted, pm_approved, approved, rejected
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pm_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pm_approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'requisition_no']);
            $table->index(['company_id', 'project_id', 'status']);
        });

        Schema::create('mr_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_requisition_id')->constrained('material_requisitions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('cost_code_id')->nullable()->constrained('cost_codes')->nullOnDelete();
            $table->decimal('quantity_requested', 18, 4);
            $table->decimal('quantity_approved', 18, 4)->default(0.0000);
            $table->decimal('quantity_ordered', 18, 4)->default(0.0000);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('material_requisition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mr_items');
        Schema::dropIfExists('material_requisitions');
    }
};
