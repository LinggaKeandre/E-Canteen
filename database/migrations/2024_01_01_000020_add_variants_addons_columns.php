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
        // Add has_variants and has_addons to menus table
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('has_variants')->default(false)->after('status');
            $table->boolean('has_addons')->default(false)->after('has_variants');
        });

        // Add variant_name and addons_json to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->after('price');
            $table->json('addons_json')->nullable()->after('variant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['has_variants', 'has_addons']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_name', 'addons_json']);
        });
    }
};

