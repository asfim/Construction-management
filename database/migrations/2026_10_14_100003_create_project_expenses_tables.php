<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cost_code_id')->constrained('cost_codes')->cascadeOnDelete();
            $table->string('voucher_number', 50);
            $table->date('expense_date');
            $table->string('payee_name');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'bkash_nagad'])->default('cash');
            $table->decimal('amount', 18, 2);
            $table->text('description');
            $table->string('reference_no')->nullable();
            $table->enum('status', ['draft', 'approved', 'paid'])->default('approved');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'voucher_number']);
            $table->index(['company_id', 'project_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};
