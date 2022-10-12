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
        //
        Schema::create('workers', function (Blueprint $table) {
        
            $table->Increments('id');
            $table->string('worktype'); //worker
            $table->string('cedula'); //DNI (Identity national document)
            $table->string('password'); //Encrypted password bcrypt()
            $table->string('name'); //Name
            $table->string('name2')->nullable(); //Second name
            $table->string('lastname1'); //Last name
            $table->string('lastname2'); //Last name
            $table->string('idaddress'); //Reference to Address
            $table->string('email')->unique(); //Unique email
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        //
        Schema::dropIfExists('workers');
    }
};
