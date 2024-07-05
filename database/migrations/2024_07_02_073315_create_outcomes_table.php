<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcomes', function (Blueprint $table) {
            //表格包括年度、类型、考生编号（唯一）、录取类别、入学方式、归属单位、招生专业、学生姓名、实际指导老师、招生指标对应老师、状态、操作人
            $table->id();
            $table->string('year')->comment('年度')->default('');
            $table->string('type')->default('');
            $table->string('student_id')->unique()->default('')->nullable();
            $table->string('admission_category')->default('')->nullable();
            $table->string('enrollment_method')->default('')->nullable();
            $table->string('affiliation_unit')->default('');
            $table->string('admission_major')->default('');
            $table->string('student_name')->default('');
            $table->string('actual_guidance_teacher')->default('');
            $table->string('teacher')->default('');
            $table->tinyInteger('check_status')->comment('核对情况')->default(0);
            $table->string('operator')->comment('操作人')->default('');
            $table->string('remark')->comment('备注')->default('')->nullable();
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
        Schema::dropIfExists('outcomes');
    }
}
