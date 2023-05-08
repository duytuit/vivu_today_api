<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampainDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campain_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('campain_id');
            $table->string('type');
            $table->integer('type_campain');
            $table->string('view');
            $table->integer('status');
            $table->string('contact');
            $table->longText('reason');
            $table->longText('content');
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
        Schema::dropIfExists('campain_details');
    }
}
