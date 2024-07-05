<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limit', function (Blueprint $table) {
            //表格包括年度、教师、时间、类型、录取类别、入学方式、相应明细、数量、归属单位、招生专业、核对情况、备注
            $table->id();
            $table->string('year')->comment('年度')->default('');
            $table->string('teacher')->comment('教师')->default('');
            $table->string('time')->comment('时间')->default('')->nullable();
            $table->string('type')->comment('类型')->default('');
            $table->string('admission_type')->comment('录取类别')->default('');
            $table->string('enrollment_method')->comment('入学方式')->default('');
            $table->string('detail')->comment('相应明细')->default('');
            $table->integer('number')->comment('数量')->default(0);
            $table->string('affiliation_unit')->comment('归属单位')->default('');
            $table->string('profession')->comment('招生专业')->default('');
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
        Schema::dropIfExists('limit');
    }
}
