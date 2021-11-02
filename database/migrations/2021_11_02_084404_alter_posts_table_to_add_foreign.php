<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPostsTableToAddForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->bigInteger('when_to_post_id')->unsigned()->nullable();
            $table->foreign('when_to_post_id')->references('id')->on('when_to_posts')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('who_can_see_post_id')->unsigned()->nullable();
            $table->foreign('who_can_see_post_id')->references('id')->on('who_can_see_posts')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropColumn('who_can_see_post_id');
            $table->dropColumn('when_to_post_id');
        });
    }
}
