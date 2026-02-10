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
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('quote_prefix')->default('QUO')->after('invoice_reset_yearly');
            $table->string('quote_format')->default('{prefix}-{year}-{number}')->after('quote_prefix');
            $table->unsignedTinyInteger('quote_number_padding')->default(5)->after('quote_format');
            $table->boolean('quote_reset_yearly')->default(true)->after('quote_number_padding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['quote_prefix', 'quote_format', 'quote_number_padding', 'quote_reset_yearly']);
        });
    }
};
