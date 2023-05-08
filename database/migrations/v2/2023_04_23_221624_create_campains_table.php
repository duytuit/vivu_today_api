<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('type')->comment('cac loai nhu: post, bill,...');
            $table->integer('type_id')->nullable()->comment('id cua post gui neu co');
            $table->json('total')->comment('tổng thông báo phải gửi');
            $table->json('status');
            $table->integer('run')->comment('1:thực thi,0:chưa thực thi');
            $table->integer('sms_sended');
            $table->integer('email_sended');
            $table->integer('app_sended');
            $table->softDeletes();
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
        Schema::dropIfExists('campains');
    }
}
