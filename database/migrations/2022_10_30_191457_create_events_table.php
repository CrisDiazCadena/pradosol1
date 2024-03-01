<?php

use App\Models\Event;
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
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title'); //event name
            $table->string('description', 1000); //event
            $table->date('start_date'); //event´s start date
            $table->date('end_date'); //event´s end date
            $table->string('main_image') -> nullable();
            $table->string('secondary_image') -> nullable();
            $table->string('third_image') -> nullable();
            $table->string('background_image') -> nullable();
            $table->string('check') -> default(Event::NO_CHECKED);
            $table->string('status')->default(Event::STATUS_WAITING);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete("restrict")->nullable(); //Reference to partners table
            $table->timestamps();//create timer, modified timer
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
