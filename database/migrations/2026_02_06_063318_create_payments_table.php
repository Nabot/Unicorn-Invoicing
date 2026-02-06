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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('method', ['cash', 'eft', 'card', 'other'])->default('eft');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('payment_date');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
