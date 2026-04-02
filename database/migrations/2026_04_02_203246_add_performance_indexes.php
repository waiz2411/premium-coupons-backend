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
        Schema::table('stores', function (Blueprint $table) {
            $table->index('is_featured');
            $table->index('category_id');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('is_trending');
            $table->index('is_new');
            $table->index('is_deal');
            $table->index('store_id');
            $table->index('category_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_trending');
            $table->index('store_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['is_trending']);
            $table->dropIndex(['is_new']);
            $table->dropIndex(['is_deal']);
            $table->dropIndex(['store_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_trending']);
            $table->dropIndex(['store_id']);
            $table->dropIndex(['category_id']);
        });
    }
};
