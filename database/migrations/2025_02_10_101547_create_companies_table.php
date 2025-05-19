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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinces_id')->nullable()->constrained('provinces')->cascadeOnUpdate();
            $table->foreignId('regencies_id')->nullable()->constrained('regencies')->cascadeOnUpdate();
            $table->foreignId('districts_id')->nullable()->constrained('districts')->cascadeOnUpdate();
            $table->foreignId('villages_id')->constrained('villages')->cascadeOnUpdate();
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
