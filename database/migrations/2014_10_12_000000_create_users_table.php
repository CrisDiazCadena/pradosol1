<?php

use App\Models\User;
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

            $table->increments('id');
            $table->string('image')->default('https://cdn-icons-png.flaticon.com/512/74/74472.png'); // Avatar
            $table->string('identification_card')->unique();
            $table->string('name');
            $table->string('lastname');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('verification_token')->nullable();
            $table->string('status')->default(User::INACTIVE_STATUS);// Verificacion de la sociedad
            $table->string('verified')->default(User::NOT_VERIFIED_USER);// Verificacion del Usuario
            $table->integer('validation_status_id')->unsigned();//Validacion de los datos del socio
            $table->integer('type_identification_id')->unsigned();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('type_identification_id')->references('id')->on('type_identification_users')->onDelete('cascade');
            $table->foreign('validation_status_id')->references('id')->on('validation_status_users')->onDelete('cascade')->default(2);

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
