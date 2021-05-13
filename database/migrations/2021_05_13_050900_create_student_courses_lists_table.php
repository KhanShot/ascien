<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCoursesListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_courses_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')
                ->on('courses')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')->references('id')
                ->on('payments')->cascadeOnDelete()->cascadeOnUpdate();

            $table->boolean("is_gift")->default(false);
            $table->unsignedBigInteger('gift_from')->nullable();
            $table->foreign('gift_from')->references('id')
                ->on('users')->cascadeOnDelete()->cascadeOnUpdate();


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
        Schema::dropIfExists('student_courses_lists');
    }
}
