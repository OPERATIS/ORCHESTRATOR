<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultBoolean extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('integrations', function (Blueprint $table) {
            $table->boolean('actual')->default(true)->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('show')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('integrations', function (Blueprint $table) {
            $table->boolean('actual')->nullable()->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('show')->nullable()->change();
        });
    }
}
