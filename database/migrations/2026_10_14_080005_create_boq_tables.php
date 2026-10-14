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
        Schema::create('boq_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('title')->default('Original Baseline BOQ');
            $table->decimal('total_amount', 18, 2)->default(0.00);
            $table->string('status', 30)->default('draft'); // draft, under_review, approved, superseded
            $table->boolean('is_locked')->default(false); // Once approved, permanently locked
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'version_number']);
            $table->index(['company_id', 'project_id', 'status']);
        });

        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_version_id')->constrained('boq_versions')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('cost_code_id')->nullable()->constrained('cost_codes')->nullOnDelete();
            $table->string('item_code', 50); // e.g., 01.01.001
            $table->text('description'); // Specification of civil item
            $table->string('unit', 30); // sft, cft, cum, bag, ton, etc.
            $table->decimal('planned_quantity', 18, 4)->default(0.0000);
            $table->decimal('unit_rate', 18, 2)->default(0.00);
            $table->decimal('total_amount', 18, 2)->default(0.00);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['boq_version_id', 'sort_order']);
            $table->index(['project_id', 'item_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_items');
        Schema::dropIfExists('boq_versions');
    }
};
