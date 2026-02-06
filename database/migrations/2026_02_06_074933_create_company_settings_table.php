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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();
            $table->string('invoice_prefix', 10)->default('INV');
            $table->string('invoice_format')->default('{prefix}-{year}-{number}'); // Format: {prefix}-{year}-{number}, {prefix}-{number}, etc.
            $table->integer('invoice_number_padding')->default(5); // Number of digits for padding (e.g., 5 = 00001)
            $table->boolean('invoice_reset_yearly')->default(true); // Reset invoice numbers each year
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
