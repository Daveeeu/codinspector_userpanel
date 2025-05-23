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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('event');
            $table->bigInteger('store_id')->unsigned()->nullable();
            $table->boolean('read')->default(false);
            $table->boolean('deleted')->default(false);
            $table->string('deleted_store_domain')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('store_id')->references('store_id')->on('stores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
