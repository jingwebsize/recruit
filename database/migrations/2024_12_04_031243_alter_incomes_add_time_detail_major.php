<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIncomesAddTimeDetailMajor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            //增加下达时间、首批学校明细、招生专业、入学方式
            $table->string('time')->nullable()->comment('下达时间');
            $table->string('school_detail')->nullable()->comment('首批学校明细');
            $table->string('profession')->nullable()->comment('招生专业');
            $table->string('enrollment_method')->nullable()->comment('入学方式');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            //
            $table->dropColumn(['time', 'school_detail', 'profession', 'enrollment_method']);
        });
    }
}
