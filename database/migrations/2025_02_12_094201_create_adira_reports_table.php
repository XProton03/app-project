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
        Schema::create('adira_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees')->cascadeOnUpdate();
            $table->string('number')->nullable();
            $table->date('periode')->nullable();
            $table->string('status_tiket')->nullable();
            $table->string('category')->nullable();
            $table->string('service')->nullable();
            $table->string('subject')->nullable();
            $table->string('responses_duration')->nullable();
            $table->string('responses_breach')->nullable();
            $table->string('resolution_duration')->nullable();
            $table->string('resolution_breach')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adira_reports');
    }
};
