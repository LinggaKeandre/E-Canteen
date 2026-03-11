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
            $table->enum('cancel_request', ['none', 'pending', 'accepted', 'rejected'])->default('none')->after('is_auto_confirmed');
            $table->timestamp('cancel_requested_at')->nullable()->after('cancel_request');
            $table->timestamp('cancel_responded_at')->nullable()->after('cancel_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_request', 'cancel_requested_at', 'cancel_responded_at']);
        });
    }
};
