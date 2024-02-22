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
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id('vehicle_id');
            $table->string('vehicle_name');
            $table->string('vehicle_serial_number')->nullable();
            $table->date('vehicle_date_purchase');
            $table->string('vehicle_chasis_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_battery_number')->nullable();
            $table->string('vehicle_motor_number')->nullable();
            $table->string('vehicle_customer_name')->nullable();
            $table->integer('vehicle_isactive')->default(1)->nullable();
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
        Schema::dropIfExists('vehicle');
    }
};
