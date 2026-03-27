<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('about')->nullable();
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->float('rating')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('affiliate_link')->nullable();
            $table->json('countries')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('is_deal')->default(false);
            $table->string('discount')->nullable();
            $table->text('description')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('success_rate')->default(100);
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_new')->default(false);
            $table->json('countries')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_trending')->default(false);
            $table->json('countries')->nullable();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->date('date')->nullable();
            $table->string('category')->nullable();
            $table->string('read_time')->nullable();
            $table->timestamps();
        });

        Schema::create('custom_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hero_title');
            $table->text('hero_description')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('selected_coupon_ids')->nullable();
            $table->boolean('is_subpage')->default(false);
            $table->foreignUuid('parent_page_id')->nullable()->constrained('custom_pages')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->string('code', 2)->primary();
            $table->string('name');
            $table->json('hero_images')->nullable();
            $table->json('featured_store_ids')->nullable();
            $table->json('trending_coupon_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->string('page_key')->primary();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('url_slug')->nullable();
            $table->timestamps();
        });

        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('action')->nullable();
            $table->string('country_code')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logs');
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('custom_pages');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('categories');
    }
};
