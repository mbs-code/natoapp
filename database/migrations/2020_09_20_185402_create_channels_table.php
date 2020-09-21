<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique(); // UCxxx
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('playlist', 255)->nullable(); // UUxxx
            $table->string('thumbnail_url', 255)->nullable();
            $table->string('banner_url', 255)->nullable();

            $table->datetime('published_at'); // 作成日時

            $table->integer('views')->unsigned(); // 再生数
            $table->integer('comments')->unsigned(); // コメント数
            $table->integer('subscribers')->unsigned(); // 登録者数
            $table->integer('videos')->unsigned(); // 動画数

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
        Schema::dropIfExists('channels');
    }
}
