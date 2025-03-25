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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique coupon code
            $table->enum('type', ['percentage', 'fixed']); // Discount type
            $table->decimal('value', 8, 2); // Discount value
            $table->integer('max_uses')->nullable(); // Maximum number of uses globally
            $table->integer('max_uses_per_user')->nullable(); // Maximum number of uses per user
            $table->date('start_date')->nullable(); // Start date of validity
            $table->date('end_date')->nullable(); // End date of validity
            $table->boolean('status')->default(true); // Active or inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
