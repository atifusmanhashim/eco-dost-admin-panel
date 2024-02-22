<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('part_name')->nullable();
            $table->string('description')->nullable();
            $table->string('make')->nullable();
            $table->string('supported_models')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('status')->default(1);
            $table->float('price',8,2)->nullable();
            $table->date('manufacture_date')->nullable();
            $table->integer('warranty_period')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};
