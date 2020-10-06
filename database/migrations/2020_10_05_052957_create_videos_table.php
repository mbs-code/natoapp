<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\VideoType;
use App\Enums\VideoStatus;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->morphs('channel');

            $table->string('code', 32)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 255)->nullable();
            $table->enum('type', VideoType::keys()); // video, upcoming, live とか
            $table->enum('status', VideoStatus::keys()); // puiblic, unlisted とか
            $table->integer('duration')->unsigned(); // seconds

            $table->string('tags')->nullable(); // csv array
            $table->unsignedInteger('max-viewers')->nullable();
            $table->datetime('published_at'); // 作成日時

            $table->datetime('start_time')->nullable(); // 集計用 buffer
            $table->datetime('end_time')->nullable(); // 集計用 buffer
            $table->datetime('scheduled_start_time')->nullable();
            $table->datetime('scheduled_end_time')->nullable();
            $table->datetime('actual_start_time')->nullable();
            $table->datetime('actual_end_time')->nullable();

            $table->unsignedInteger('views')->nullable();
            $table->unsignedInteger('likes')->nullable();
            $table->unsignedInteger('dislikes')->nullable();
            $table->unsignedInteger('favorites')->nullable();
            $table->unsignedInteger('comments')->nullable();
            $table->unsignedInteger('concurrent_viewers')->nullable();

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
        Schema::dropIfExists('videos');
    }
}
