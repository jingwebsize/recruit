<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLimitAddDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('limit', function (Blueprint $table) {
            //增加招生研究所
            $table->string('department')->nullable()->after('teacher');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('limit', function (Blueprint $table) {
            //
            $table->dropColumn('department');
        });
    }
}
