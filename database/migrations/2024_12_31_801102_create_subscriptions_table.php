<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/YYYY_MM_DD_HHMMSS_create_subscriptions_table.php
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('subscription_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('package_id')->references('package_id')->on('packages')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('auto_renewal')->default(true);
            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active');
            $table->timestamps(0);
        });


    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }

};
