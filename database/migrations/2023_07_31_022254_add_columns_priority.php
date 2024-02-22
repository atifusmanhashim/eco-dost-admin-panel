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
        Schema::table('priority', function (Blueprint $table) {
            $table->integer('priority_lvl')->nullable();
            $table->integer('priority_isactive')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('priority', function (Blueprint $table) {
            $table->dropColumn('priority_lvl');
            $table->dropColumn('priority_isactive');
        });
    }
};
