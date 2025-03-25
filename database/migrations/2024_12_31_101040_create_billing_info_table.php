<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/YYYY_MM_DD_HHMMSS_create_billing_info_table.php
    public function up()
    {
        Schema::create('billing_info', function (Blueprint $table) {
            $table->id('billing_id');
            $table->foreignId('user_id')->constrained('users');
            $table->string('company_name', 100);
            $table->string('tax_id', 50);
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('postal_code', 20);
            $table->string('country', 50);
            $table->timestamps(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_info');
    }

};
