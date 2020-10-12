<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_id'); // foreign key にはしない

            $table->unsignedInteger('views')->nullable();
            $table->unsignedInteger('likes')->nullable();
            $table->unsignedInteger('dislikes')->nullable();
            $table->unsignedInteger('favorites')->nullable();
            $table->unsignedInteger('comments')->nullable();
            $table->unsignedInteger('concurrent_viewers')->nullable();

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
        Schema::dropIfExists('video_stats');
    }
}
