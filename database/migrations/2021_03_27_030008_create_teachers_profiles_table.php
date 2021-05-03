<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers_profiles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string("specialty",50)->nullable();
            $table->string("education",100)->nullable();
            $table->string("teaching_experience",10)->nullable();
            $table->json("achievements")->nullable();
            $table->date("birthday")->nullable();
            $table->json("contacts")->nullable();
            $table->text("about")->nullable();
            $table->json("education_format")->nullable();
            $table->string("videomakeing_experience",20)->nullable();
            $table->string("auditory",20)->nullable();
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
        Schema::dropIfExists('teachers_profiles');
    }
}
