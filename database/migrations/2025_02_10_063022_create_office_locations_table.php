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
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinces_id')->constrained('provinces')->cascadeOnUpdate();
            $table->foreignId('regencies_id')->constrained('regencies')->cascadeOnUpdate();
            $table->foreignId('districts_id')->constrained('districts')->cascadeOnUpdate();
            $table->foreignId('villages_id')->constrained('villages')->cascadeOnUpdate();
            $table->string('code')->nullable();
            $table->string('office_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_locations');
    }
};
