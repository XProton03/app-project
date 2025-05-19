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
        Schema::create('quotation_payments', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date')->nullable();
            $table->string('payment_number')->nullable();
            $table->foreignId('quotations_id')->constrained('quotations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('users_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_payments');
    }
};
