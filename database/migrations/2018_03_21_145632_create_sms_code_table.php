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
            $table->tinyInteger('type')->nullable()->default(1);
            $table->tinyInteger('status')->default(0);
            $table->string('ip', 32)->nullable()->default(null);
            $table->string('result', 128)->nullable()->default(null);
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
