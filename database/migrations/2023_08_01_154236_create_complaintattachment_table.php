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
        Schema::create('complaintattachment', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->bigInteger('complaint_id')->unsigned()->index()->nullable();
            $table->foreign('complaint_id')->references('id')->on('complaint')->onDelete('cascade');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('complaintattachment');
    }
};
