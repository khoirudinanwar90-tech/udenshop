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
        // 1. Brands Table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });

        // 2. Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Sub Categories Table
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 4. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('SKU')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->double('weight')->default(0.0); // in kg or grams
            $table->string('dimensions')->nullable(); // e.g. "10x10x10"
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('video_url')->nullable();
            $table->string('type')->default('physical'); // physical, digital, variant, bundle, affiliate
            $table->string('affiliate_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Product Images Table
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Carts Table
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->timestamps();
        });

        // 7. Cart Items Table
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->json('options')->nullable(); // For variation specs (color, size)
            $table->timestamps();
        });

        // 8. Coupons Table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('fixed'); // fixed, percent
            $table->decimal('value', 15, 2);
            $table->decimal('min_spend', 15, 2)->default(0.00);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('shipping_cost', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, processing, shipped, completed, cancelled
            $table->text('shipping_address');
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        // 11. Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('transaction_id')->nullable();
            $table->string('payment_method');
            $table->string('payment_gateway')->default('manual'); // midtrans, xendit, doku, bank_transfer
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, success, failed, refunded
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // 12. Shipments Table
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('courier');
            $table->string('service');
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->json('shipment_logs')->nullable();
            $table->timestamps();
        });

        // 13. Reviews Table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 14. Wishlists Table
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        // 15. Blogs Table
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 16. Banners Table
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('type')->default('hero'); // hero, promo
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 17. Affiliates Table
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('referral_code')->unique();
            $table->decimal('total_earnings', 15, 2)->default(0.00);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 18. Commissions Table
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();
        });

        // 19. Loyalty Points Table
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('points');
            $table->string('type')->default('earn'); // earn, redeem
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 20. Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
    }
};
