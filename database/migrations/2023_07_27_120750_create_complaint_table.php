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
        Schema::create('complaint', function (Blueprint $table) {
            $table->id();
            $table->string('part_name');
            $table->string('description');
            $table->string('make');
            $table->string('model');
            $table->string('chasis_number');
            $table->integer('complaint_isactive')->nullable()->default(1);
            $table->string('complaint_status')->nullable()->default('I');
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
        Schema::dropIfExists('complaint');
    }
};
