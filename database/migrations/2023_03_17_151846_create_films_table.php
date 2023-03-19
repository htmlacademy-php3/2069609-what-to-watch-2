<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('poster_image', 255)->nullable();
            $table->string('preview_image', 255)->nullable();
            $table->string('background_image', 255)->nullable();
            $table->string('background_color', 9)->nullable();
            $table->string('video_link', 255)->nullable();
            $table->string('preview_video_link', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('director', 255)->nullable();
            $table->integer('run_time')->nullable();
            $table->integer('released')->nullable();
            $table->string('imdb_id')->unique();
            $table->enum('status', ['pending', 'on moderation', 'ready']);
            $table->float('rating')->nullable();
            $table->integer('scores_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
