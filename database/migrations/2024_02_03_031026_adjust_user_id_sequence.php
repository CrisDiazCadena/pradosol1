<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Desactiva la revisión de claves foráneas si es necesario
        Schema::disableForeignKeyConstraints();

        // Establece el valor inicial de autoincrement en 10
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        // Reactiva la revisión de claves foráneas si se desactivó
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
