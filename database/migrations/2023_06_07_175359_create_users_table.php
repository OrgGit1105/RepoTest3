<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->collation('utf8_general_ci');
            $table->string('email')->collation('utf8_general_ci');
            $table->string('password')->collation('utf8_general_ci');
            $table->integer('role_id');
            $table->string('jwt_active')->collation('utf8_general_ci');
            $table->timestamp('retirement date')->nullable();
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
        Schema::dropIfExists('users');
    }
}
