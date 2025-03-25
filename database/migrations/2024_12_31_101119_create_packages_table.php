<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id('package_id');
            $table->string('name', 100)->unique();
            $table->text('description');
            $table->integer('query_limit');
            $table->decimal('cost_per_query', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('permissions');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }

};
