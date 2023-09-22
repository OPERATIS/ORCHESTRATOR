<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ga_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connect_id')->index();
            $table->unsignedBigInteger('profile_id')->index();
            $table->string('name');
            $table->string('currency');
            $table->string('timezone');
            $table->boolean('actual')->nullable()->index();
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
        Schema::dropIfExists('ga_profiles');
    }
}
