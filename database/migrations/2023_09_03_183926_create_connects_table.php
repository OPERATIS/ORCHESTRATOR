<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('platform')->index();
            $table->string('access_token', 2048);
            $table->string('refresh_token', 2048)->nullable();
            $table->unsignedBigInteger('expires_in')->nullable();
            $table->string('scope', 2048)->nullable();
            $table->string('app_user_slug', 2048)->nullable();
            $table->string('app_user_id', 2048)->nullable();
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
        Schema::dropIfExists('connects');
    }
}
