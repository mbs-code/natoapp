<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('twitter_id'); // foreign key にはしない

            $table->unsignedInteger('followers')->nullable(); // フォロワー
            $table->unsignedInteger('friends')->nullable(); // フォロー
            $table->unsignedInteger('listed')->nullable(); // リストに入ってる数
            $table->unsignedInteger('favourites')->nullable(); // いいね数
            $table->unsignedInteger('statuses')->nullable(); // ツイート数

            $table->datetime('created_at')->nullable(); // system
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_stats');
    }
}
