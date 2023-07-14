<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emotions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('arriving_id');
            $table->timestamp('time');
            $table->string('type_check')->comment('in, out');
            $table->float('happy')->nullable();
            $table->float('sad')->nullable();
            $table->float('angry')->nullable();
            $table->float('confused')->nullable();
            $table->float('disgusted')->nullable();
            $table->float('surprised')->nullable();
            $table->float('calm')->nullable();
            $table->float('fear')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emotions');
    }
}
