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
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Back-populate
        $stores = \App\Models\Store::all();
        foreach ($stores as $store) {
            $baseSlug = \Illuminate\Support\Str::slug($store->name . '-promo-code');
            $uniqueSlug = $baseSlug;
            $count = 1;
            while (\App\Models\Store::where('slug', $uniqueSlug)->where('id', '!=', $store->id)->exists()) {
                $uniqueSlug = $baseSlug . '-' . $count;
                $count++;
            }
            $store->update(['slug' => $uniqueSlug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
