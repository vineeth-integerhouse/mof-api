<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPostsTableAddStatus extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->time('time')->nullable();
            $table->date('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('time');
            $table->dropColumn('date');
        });
    }
}
