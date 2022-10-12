<?php

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
        Schema::create('users', function (Blueprint $table) {
            
            $table->engine="InnoDB"; //Specify the table storage engine
            $table->Increments('id');
            $table->string('usertype'); //user (administrator, normal user, particular)
            $table->string('cedula'); //DNI (Identity national document)
            $table->string('password'); //Encrypted password bcrypt()
            $table->string('name'); //Name
            $table->string('name2')->nullable(); //Second name
            $table->string('lastname1'); //Last name
            $table->string('lastname2'); //Last name
            $table->binary('state')->nullable(); //state ('Active, Inactive')
            $table->string('address'); //Reference to address
            $table->string('email')->unique(); //Unique email
            $table->integer('pass')->nullable(); //Number of active annual passes
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
