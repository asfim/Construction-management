<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number', 50);
            $table->date('bill_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('advance_recovery_percentage', 5, 2)->default(10.00);
            $table->decimal('advance_recovery_amount', 18, 2)->default(0);
            $table->decimal('retention_percentage', 5, 2)->default(5.00);
            $table->decimal('retention_amount', 18, 2)->default(0);
            $table->decimal('vat_percentage', 5, 2)->default(7.50);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('net_payable', 18, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'certified', 'paid'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('certified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('certified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'bill_number']);
            $table->index(['company_id', 'project_id', 'bill_date']);
        });

        Schema::create('client_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_bill_id')->constrained('client_bills')->cascadeOnDelete();
            $table->foreignId('boq_item_id')->constrained('boq_items')->cascadeOnDelete();
            $table->text('description');
            $table->string('unit', 20);
            $table->decimal('unit_rate', 18, 2);
            $table->decimal('previous_quantity', 18, 4)->default(0);
            $table->decimal('current_quantity', 18, 4)->default(0);
            $table->decimal('total_quantity', 18, 4)->default(0);
            $table->decimal('current_amount', 18, 2)->default(0);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_bill_items');
        Schema::dropIfExists('client_bills');
    }
};
