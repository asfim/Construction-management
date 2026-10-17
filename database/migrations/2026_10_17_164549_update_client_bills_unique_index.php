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
        Schema::table('client_bills', function (Blueprint $table) {
            $table->dropUnique('client_bills_company_id_bill_number_unique');
            $table->unique(['company_id', 'project_id', 'bill_number'], 'client_bills_company_project_bill_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_bills', function (Blueprint $table) {
            $table->dropUnique('client_bills_company_project_bill_unique');
            $table->unique(['company_id', 'bill_number'], 'client_bills_company_id_bill_number_unique');
        });
    }
};
