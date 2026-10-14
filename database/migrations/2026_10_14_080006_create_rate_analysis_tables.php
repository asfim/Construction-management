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
        Schema::create('rate_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('unit', 30)->default('unit');
            $table->decimal('material_cost', 18, 2)->default(0.00);
            $table->decimal('labor_cost', 18, 2)->default(0.00);
            $table->decimal('equipment_cost', 18, 2)->default(0.00);
            $table->decimal('overhead_percentage', 5, 2)->default(5.00);
            $table->decimal('profit_percentage', 5, 2)->default(10.00);
            $table->decimal('wastage_percentage', 5, 2)->default(3.00);
            $table->decimal('total_unit_rate', 18, 2)->default(0.00);
            $table->string('status', 30)->default('draft'); // draft, approved
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });

        Schema::create('rate_analysis_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_analysis_id')->constrained('rate_analyses')->cascadeOnDelete();
            $table->string('component_type', 30); // material, labor, equipment, transport, other
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('description'); // e.g. Mason (রাজমিস্ত্রি), Helper (জোগালী), 20mm Rebar, Stone Chips
            $table->string('unit', 30);
            $table->decimal('quantity', 18, 4)->default(0.0000);
            $table->decimal('rate', 18, 2)->default(0.00);
            $table->decimal('amount', 18, 2)->default(0.00);
            $table->timestamps();

            $table->index(['rate_analysis_id', 'component_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_analysis_components');
        Schema::dropIfExists('rate_analyses');
    }
};
