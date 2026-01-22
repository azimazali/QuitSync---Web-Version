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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('cigarettes_per_day')->default(20); // Default to a pack a day
            $table->decimal('pack_price', 8, 2)->default(10.00); // Default price
            $table->timestamp('quit_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cigarettes_per_day', 'pack_price', 'quit_date']);
        });
    }
};
