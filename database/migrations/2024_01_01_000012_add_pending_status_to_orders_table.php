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
        // Add 'pending' as a new status option
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing enum and recreate with new values
            $table->enum('status', ['pending', 'preparing', 'ready'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['preparing', 'ready'])->default('preparing')->change();
        });
    }
};
