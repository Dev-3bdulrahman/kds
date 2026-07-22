<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kds_displays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('display_type')->default('kitchen');
            $table->string('location')->nullable();
            $table->string('status')->default('online');
            $table->timestamp('last_heartbeat')->nullable();
            $table->json('display_categories')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('status');
            $table->index('display_type');
        });

        Schema::create('kds_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('pos_sale_id')->constrained('pos_sales')->onDelete('cascade');
            $table->foreignId('display_id')->nullable()->constrained('kds_displays')->onDelete('set null');
            $table->string('order_number');
            $table->string('table_number')->nullable();
            $table->integer('guest_count')->nullable();
            $table->string('status')->default('pending');
            $table->string('priority')->default('normal');
            $table->integer('preparation_time')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('display_id');
            $table->index('pos_sale_id');
            $table->index('status');
            $table->index('priority');
        });

        Schema::create('kds_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kds_order_id')->constrained('kds_orders')->onDelete('cascade');
            $table->foreignId('pos_sale_item_id')->constrained('pos_sale_items')->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_name_ar')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->json('modifiers')->nullable();
            $table->string('status')->default('pending');
            $table->integer('preparation_time')->default(0);
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('kds_order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kds_order_items');
        Schema::dropIfExists('kds_orders');
        Schema::dropIfExists('kds_displays');
    }
};
