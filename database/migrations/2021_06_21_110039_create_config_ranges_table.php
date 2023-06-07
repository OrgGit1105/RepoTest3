<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_ranges', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('from')->nullable();
            $table->integer('to')->nullable();
            $table->tinyInteger('rank')->nullable();
            $table->float('number_of_studies',20,15 )->nullable();
            $table->float('average_total', 20,15)->nullable();
            $table->string('ranges')->nullable();
            $table->integer('type')->nullable();
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
        Schema::dropIfExists('config_ranges');
    }
}
