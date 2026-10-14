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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('email');
            $table->foreignId('current_company_id')->nullable()->after('phone')->constrained('companies')->nullOnDelete();
            $table->unsignedBigInteger('current_project_id')->nullable()->after('current_company_id');
            $table->string('designation', 100)->nullable()->after('current_project_id');
            $table->string('avatar')->nullable()->after('designation');
            $table->string('preferred_locale', 10)->default('bn')->after('avatar');
            $table->boolean('is_active')->default(true)->after('preferred_locale');
            $table->softDeletes()->after('updated_at');

            $table->index(['current_company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_company_id']);
            $table->dropColumn([
                'phone',
                'current_company_id',
                'current_project_id',
                'designation',
                'avatar',
                'preferred_locale',
                'is_active',
                'deleted_at',
            ]);
        });
    }
};
