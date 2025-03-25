<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/YYYY_MM_DD_HHMMSS_create_stores_table.php
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id('store_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('platform_id')->constrained('platforms')->references('platform_id');
            $table->string('store_name', 100);
            $table->string('domain', 255)->unique();
            $table->decimal('lost_package_cost', 10, 2)->default(0.00);
            $table->foreignId('subscription_id')->constrained('subscriptions')->references('subscription_id')->onDelete('cascade');
            $table->foreignId('billing_id')->constrained('billing_info')->references('billing_id')->onDelete('cascade');
            $table->timestamps(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stores');
    }


    /**
     * Reverse the migrations.
     */
};
