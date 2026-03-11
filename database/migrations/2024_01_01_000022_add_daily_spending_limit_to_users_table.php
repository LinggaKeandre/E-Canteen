<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('daily_spending_limit', 15, 0)->nullable()->after('balance');
            $table->boolean('daily_spending_limit_enabled')->default(false)->after('daily_spending_limit');
            $table->timestamp('daily_spending_limit_resets_at')->nullable()->after('daily_spending_limit_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_spending_limit',
                'daily_spending_limit_enabled',
                'daily_spending_limit_resets_at',
            ]);
        });
    }
};

