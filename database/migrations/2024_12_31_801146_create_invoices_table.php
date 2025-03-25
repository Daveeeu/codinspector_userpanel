<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/YYYY_MM_DD_HHMMSS_create_invoices_table.php
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('subscription_id')->references('subscription_id')->on('subscriptions')->onDelete('cascade');  // Manually define the foreign key
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->timestamps(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }

};
