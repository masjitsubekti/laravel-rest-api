<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_sistem', 100);
            $table->string('instansi', 100);
            $table->text('alamat');
            $table->string('email', 50);
            $table->string('telepon', 15);
            $table->string('fax', 15);
            $table->string('url_root', 30);
            $table->string('logo', 30);
            $table->string('favicon', 30);
            $table->boolean('status');
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
        Schema::dropIfExists('slider');
    }
}
