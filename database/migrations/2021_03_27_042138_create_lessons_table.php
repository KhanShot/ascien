<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->references('id')
                ->on('sections')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string("title",100);
            $table->text("description")->nullable();
            $table->string("content_type", 10)->nullable();
            $table->string("video_url")->nullable();
            $table->string("presentation_file")->nullable();
            $table->longText("article_text")->nullable();
            $table->json("resources")->nullable();

            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
