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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->integer('balance')->default(0)->after('role');
            $table->string('phone_number')->nullable()->after('balance');
            $table->string('username')->unique()->nullable()->after('phone_number');
            $table->string('profile_photo')->nullable()->after('username');
            $table->text('description')->nullable()->after('profile_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'balance', 'phone_number', 'username', 'profile_photo', 'description']);
        });
    }
};
