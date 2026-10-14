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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_contact_person')->nullable();
            $table->string('client_phone', 50)->nullable();
            $table->string('client_email')->nullable();
            $table->text('site_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('division', 100)->nullable();
            $table->decimal('contract_value', 18, 2)->default(0.00);
            $table->decimal('budget_amount', 18, 2)->default(0.00);
            $table->decimal('retention_percentage', 5, 2)->default(5.00);
            $table->decimal('advance_recovery_percentage', 5, 2)->default(10.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('revised_end_date')->nullable();
            $table->string('status', 30)->default('planning'); // planning, active, on_hold, completed, archived
            $table->decimal('progress_percent', 5, 2)->default(0.00);
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
