<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('smoking_logs', function (Blueprint $table) {
            $table->float('sentiment_score')->nullable();
            $table->float('sentiment_magnitude')->nullable();
            $table->string('risk_level')->nullable(); // high, moderate, low
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smoking_logs', function (Blueprint $table) {
            //
        });
    }
};
