<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOutcomesAddStudentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outcomes', function (Blueprint $table) {
            //增加录取批次、学生性别、学生高校、学校属性、学生专业、学生联系电话、学生邮箱
            $table->string('admission_batch')->nullable()->comment('录取批次');
            $table->string('student_gender')->nullable()->comment('学生性别');
            $table->string('student_college')->nullable()->comment('学生高校');
            $table->string('school_attributes')->nullable()->comment('学校属性');
            $table->string('student_major')->nullable()->comment('学生专业');
            $table->string('student_phone')->nullable()->comment('学生联系电话');
            $table->string('student_email')->nullable()->comment('学生邮箱');
            //增加明细
            $table->string('detail')->nullable()->comment('归属单位下一级');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outcomes', function (Blueprint $table) {
            //删除录取批次、学生性别、学生高校、学校属性、学生专业、学生联系电话、学生邮箱
            $table->dropColumn('admission_batch');
            $table->dropColumn('student_gender');
            $table->dropColumn('student_college');
            $table->dropColumn('school_attributes');
            $table->dropColumn('student_major');
            $table->dropColumn('student_phone');
            $table->dropColumn('student_email');
            //删除明细
            $table->dropColumn('detail');

        });
    }
}
