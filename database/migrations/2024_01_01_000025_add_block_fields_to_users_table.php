<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('blocked_at')->nullable()->after('daily_limit_enabled_at');
            $table->timestamp('blocked_until')->nullable()->after('blocked_at');
            $table->string('blocked_reason')->nullable()->after('blocked_until');
            $table->unsignedBigInteger('blocked_by')->nullable()->after('blocked_reason');
            $table->enum('block_type', ['temporary', 'permanent'])->nullable()->after('blocked_by');
            
            $table->foreign('blocked_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropColumn(['blocked_at', 'blocked_until', 'blocked_reason', 'blocked_by', 'block_type']);
        });
    }
};

