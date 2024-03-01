<?php

use App\Models\UserRequest;
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
        Schema::create('user_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('title');
            $table->string('description');
            $table->string('status')->default(UserRequest::STATUS_CREATED);
            $table->string('comments')->nullable();
            $table->integer('user_id')->unsigned(); // Puede ser nula
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('admin_id')->unsigned()->nullable(); // Puede ser nula
            $table->foreign('admin_id')->references('id')->on('administrators')->onDelete('cascade');
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
        Schema::dropIfExists('user_requests');
    }
};
