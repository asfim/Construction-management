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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('transaction_type', 30); // grn_in, issue_out, return_in, return_out, transfer_in, transfer_out, adjustment
            $table->decimal('quantity', 18, 4); // positive for in, negative for out
            $table->decimal('unit_cost', 18, 2)->default(0.00);
            $table->decimal('total_cost', 18, 2)->default(0.00);
            $table->string('reference_type')->nullable(); // GoodsReceipt, StockIssue, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'warehouse_id', 'item_id']);
            $table->index(['warehouse_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('issue_no', 50); // e.g. SIN-GLS-2026-001
            $table->date('issue_date');
            $table->string('issued_to_contractor_or_labor')->nullable();
            $table->foreignId('cost_code_id')->nullable()->constrained('cost_codes')->nullOnDelete();
            $table->string('status', 30)->default('draft'); // draft, approved
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'issue_no']);
            $table->index(['company_id', 'project_id', 'status']);
        });

        Schema::create('stock_issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_issue_id')->constrained('stock_issues')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('quantity_issued', 18, 4);
            $table->decimal('unit_cost', 18, 2)->default(0.00);
            $table->decimal('total_cost', 18, 2)->default(0.00);
            $table->timestamps();

            $table->index('stock_issue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_issue_items');
        Schema::dropIfExists('stock_issues');
        Schema::dropIfExists('inventory_transactions');
    }
};
