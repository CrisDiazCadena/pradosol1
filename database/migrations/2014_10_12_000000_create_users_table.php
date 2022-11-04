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

            //$table->engine="InnoDB"; //Specify the table storage engine
            $table->increments('id');
            $table->string('image')->default('https://cdn-icons-png.flaticon.com/512/74/74472.png'); // Avatar
            $table->enum('type_identification',['cc', 'ti', 'pap', 'nip', 'nit', 'ce']); //DNI Type 'cc' cédula de ciudadania, 'ti' tarjeta de identidad, 'pap' pasaporte, 'nip' numero de identificacion personal, 'nit' numero de identificacion tributaria, 'ce' cedula de extranjeria
            $table->string('identification_card')->unique(); //DNI (Identity document)
            $table->string('password'); //Encrypted password bcrypt()
            $table->string('name'); //Name
            $table->string('lastname'); //Last name
            $table->enum('status', ['active', 'inactive'])->nullable(false)->default('inactive'); //state ('Active, Inactive')
            $table->string('address'); //Reference to address
            $table->string('email')->unique(); //Unique email
            $table->string('phone')->nullable(); //phone number
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
