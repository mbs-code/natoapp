<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtubes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique(); // UCxxx
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('playlist', 255)->nullable(); // UUxxx
            $table->string('thumbnail_url', 255)->nullable();
            $table->string('banner_url', 255)->nullable();

            $table->string('tags', 511)->nullable(); // csv array
            $table->datetime('published_at')->nullable(); // 作成日時

            // start stat fields
            $table->unsignedInteger('views')->nullable(); // 再生数
            $table->unsignedInteger('comments')->nullable(); // コメント数
            $table->unsignedInteger('subscribers')->nullable(); // 登録者数
            $table->unsignedInteger('videos')->nullable(); // 動画数
            // end stat fields

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
        Schema::dropIfExists('youtubes');
    }
}
