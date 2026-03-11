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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_paid')->default(true)->after('total_amount'); // Payment status - money is held by system
            $table->boolean('is_confirmed_by_user')->default(false)->after('is_paid'); // User confirmed receipt
            $table->boolean('is_confirmed_by_seller')->default(false)->after('is_confirmed_by_user'); // Seller confirmed handover
            $table->boolean('is_completed')->default(false)->after('is_confirmed_by_seller'); // Money transferred to seller
            $table->timestamp('completed_at')->nullable()->after('is_completed'); // When money was transferred
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'is_confirmed_by_user', 'is_confirmed_by_seller', 'is_completed', 'completed_at']);
        });
    }
};
