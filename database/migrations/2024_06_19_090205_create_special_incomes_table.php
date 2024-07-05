<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_incomes', function (Blueprint $table) {
            //表格包含年度、收入原因、收入时间、类型、数量、对应明细、核对情况、备注
            $table->id();
            $table->string('year')->default(''); 
            $table->string('reason')->default('')->nullable();
            $table->string('income_time')->default('')->nullable();
            $table->string('type')->default(''); 
            $table->integer('number')->default(0); 
            $table->string('detail')->default(''); 
            $table->tinyInteger('check')->default(0);
            $table->string('operator')->comment('操作人')->default('');  
            $table->string('remark')->default('')->nullable();
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
        Schema::dropIfExists('special_incomes');
    }
}
