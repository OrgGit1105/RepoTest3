<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryUpdatePaidOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_update_paid_off', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('type')->comment('1:auto_month|2:auto_year|3:report');
            $table->integer('report_id')->nullable();
            $table->float('paid_off_before');
            $table->float('paid_off_after');
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
        Schema::dropIfExists('history_update_paid_off');
    }
}
