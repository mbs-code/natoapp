<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubeStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('youtube_id'); // foreign key にはしない

            $table->unsignedInteger('views')->nullable(); // 再生数
            $table->unsignedInteger('comments')->nullable(); // コメント数
            $table->unsignedInteger('subscribers')->nullable(); // 登録者数
            $table->unsignedInteger('videos')->nullable(); // 動画数

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
        Schema::dropIfExists('youtube_stats');
    }
}
