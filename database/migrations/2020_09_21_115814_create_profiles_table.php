<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique(); // 同名禁止にしておく
            $table->string('kana', 255); // 検索用ひらがな
            $table->text('description')->nullable();

            $table->string('thumbnail_url', 255)->nullable(); // cache
            $table->datetime('published_at')->nullable();; // cache
            $table->unsignedInteger('followers')->nullable(); // twitter cache
            $table->unsignedInteger('subscribers')->nullable(); // youtube cache

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
        Schema::dropIfExists('profiles');
    }
}
