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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('customers_id')->constrained('customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('project_name')->nullable();
            $table->date('request_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('quotation_price')->nullable();
            $table->unsignedInteger('task_price')->nullable();
            $table->decimal('completion_percentage')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('employees_id')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
