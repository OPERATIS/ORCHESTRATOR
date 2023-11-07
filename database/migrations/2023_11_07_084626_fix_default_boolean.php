<?php

use App\Models\ChatMessage;
use App\Models\Integration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDefaultBoolean extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Integration::whereNull('actual')
            ->update(['actual' => true]);
        Schema::table('integrations', function (Blueprint $table) {
            $table->boolean('actual')->default(true)->nullable(false)->change();
        });

        ChatMessage::whereNull('show')
            ->update(['show' => true]);
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('show')->default(true)->nullable(false)->change();
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
