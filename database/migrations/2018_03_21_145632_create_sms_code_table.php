<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_code', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 60);
            $table->string('code', 10);
            $table->tinyInteger('status')->default(0);
            $table->string('ip', 32);
            $table->string('result', 128);
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
        Schema::dropIfExists('sms_code');
    }
}
