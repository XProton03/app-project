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
        Schema::create('prospect_leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('industry_type')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('pic')->nullable();
            $table->foreignId('status_leads_id')->constrained('status_leads')->cascadeOnUpdate()->nullable();
            $table->date('schedule')->nullable();
            $table->foreignId('employees_id')->constrained('employees')->cascadeOnUpdate()->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullable();
            $table->string('followup_by')->nullable();
            $table->string('feedback')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_leads');
    }
};
