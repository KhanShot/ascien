<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string("title", 80);
            $table->unsignedBigInteger("category_id");
            $table->foreign("category_id")->references('id')
                ->on('course_categories')->cascadeOnDelete();

            $table->string("short_description")->nullable();
            $table->string("language", 30)->nullable();

            $table->text("description")->nullable();
            $table->string("level")->nullable();
            $table->string("image")->nullable();
            $table->string("intro_video")->nullable();
            $table->json("requirements")->nullable();
            $table->json("what_will_learn")->nullable();
            $table->boolean("is_free")->default(false);
            $table->integer("price")->nullable();
            $table->integer("sale_price")->nullable();

            $table->string("certificate")->nullable();


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
        Schema::dropIfExists('courses');
    }
}
