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
            $table->timestamp('ready_at')->nullable()->after('status'); // When status changed to ready
            $table->boolean('is_auto_confirmed')->default(false)->after('is_confirmed_by_seller'); // Auto confirm flag
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ready_at', 'is_auto_confirmed']);
        });
    }
};
