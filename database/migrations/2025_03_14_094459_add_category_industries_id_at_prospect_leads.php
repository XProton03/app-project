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
        Schema::table('prospect_leads', function (Blueprint $table) {
            $table->foreignId('category_industries_id')->nullable()->constrained('category_industries')->cascadeOnUpdate()->nullable()->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_leads', function (Blueprint $table) {
            $table->dropForeign(['category_industries_id']);
        });
    }
};
