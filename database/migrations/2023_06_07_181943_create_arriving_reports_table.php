<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArrivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arriving_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('in_time');
            $table->timestamp('out_time')->nullable();
            $table->text('remark')->nullable();
            $table->string('registration_type')->collation('utf8_general_ci');
            $table->string('link_face_in')->collation('utf8_general_ci')->nullable();
            $table->string('link_face_out')->collation('utf8_general_ci')->nullable();
            $table->integer('status');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arriving_reports');
    }
}
