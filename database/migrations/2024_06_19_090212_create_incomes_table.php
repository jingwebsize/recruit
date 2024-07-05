<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            //表格包括年度、类型、相应明细、数量、归属单位、核对情况、备注
            $table->id();
            $table->string('year')->default(''); 
            $table->string('type')->default(''); 
            $table->string('detail')->default(''); 
            $table->integer('number')->default(0); 
            $table->string('unit')->default(''); 
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
        Schema::dropIfExists('incomes');
    }
}
