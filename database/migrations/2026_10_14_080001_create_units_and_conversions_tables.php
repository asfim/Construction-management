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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 50); // Bag, Ton, Cubic Feet, Square Feet, Kilogram, Number, Running Feet
            $table->string('name_bn', 50)->nullable(); // ব্যাগ, টন, সিএফটি, এসএফটি, কেজি, সংখ্যা, আরএফটি
            $table->string('symbol', 20); // bag, ton, cft, sft, kg, nos, rft, m3, sqm
            $table->string('type', 30)->default('unit'); // weight, volume, area, length, count
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->unique(['company_id', 'symbol']);
        });

        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('from_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('to_unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('conversion_factor', 18, 6); // e.g. 1 ton = 1000 kg, 1 bag = 50 kg, 1 m3 = 35.3147 cft
            $table->timestamps();

            $table->unique(['company_id', 'from_unit_id', 'to_unit_id'], 'unit_conv_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
        Schema::dropIfExists('units');
    }
};
