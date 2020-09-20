<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwittersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitters', function (Blueprint $table) {
            $table->id();
            $table->string('key', 32)->unique();
            $table->string('name', 255);
            $table->string('screen_name', 255); // @xxx
            $table->string('location', 255)->nullable();
            $table->string('description')->nullable();
            $table->string('url', 255)->nullable();
            $table->string('thumbnail_url', 255)->nullable();
            $table->string('banner_url', 255)->nullable();

            $table->boolean('protected'); // 鍵の有無
            $table->datetime('published_at'); // 作成日時 (api の created_at)

            $table->integer('followers')->unsigned(); // フォロワー
            $table->integer('friends')->unsigned(); // フォロー
            $table->integer('listed')->unsigned(); // リストに入ってる数
            $table->integer('favourites')->unsigned(); // いいね数
            $table->integer('statuses')->unsigned(); // ツイート数

            $table->string('last_tweet_id', 32)->nullable(); // 最後に処理したツイートID

            $table->datetime('created_at')->nullable(); // system
            $table->datetime('updated_at')->nullable(); // system
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitters');
    }
}
