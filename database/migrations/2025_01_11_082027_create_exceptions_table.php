<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); // Kapcsolat a Store táblával
            $table->string('email_hash')->nullable(); // E-mail hash (opcionális)
            $table->string('phone_hash')->nullable(); // Telefonszám hash (opcionális)
            $table->enum('type', ['allow', 'deny']); // Kivétel típusa
            $table->timestamps();

            // Egyediség biztosítása Store szinten (csak az egyik lehet unique)
            $table->unique(['store_id', 'email_hash']);
            $table->unique(['store_id', 'phone_hash']);

            // Külső kulcs kapcsolat a Store táblával
            $table->foreign('store_id')->references('store_id')->on('stores')->onDelete('cascade');
        });    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};
