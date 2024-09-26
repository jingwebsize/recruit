<?php
namespace App\Admin\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
// use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Repositories\QueryBuilderRepository;
use Illuminate\Support\Facades\Log;

// class Incomequery extends Repository
// class Incomequery extends QueryBuilderRepository
class Incomequery extends EloquentRepository
{
    protected $eloquentClass = SpecialIncome::class;
    public function get(Grid\Model $model)
    {
        // DB::enableQueryLog();
        // dd(request()->all());
        //查询special_income表中按照年度year，类型type，明细detail进行分组求和得到数量 
        $specialIncomeQuery = DB::table('special_incomes')->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
            ->groupBy('year', 'type', 'detail');
        
        //查询Income表中按照年度year，类型type，明细detail进行分组求和得到数量
        $incomeQuery = DB::table('incomes')
            ->select(DB::raw('year, type, detail, sum(number) as income_quantity'))
            ->groupBy('year', 'type', 'detail');

        // //将两个表融合
        // $mergedData = $specialIncomeData->merge($incomeData);
        //将两个表的数据融合，specialIncomeData结果为主，相同year、type、detail字段下，新增一列income_quantity，值为incomeData中对应的数量，若不存在则为0
        $query = DB::table(DB::raw("({$specialIncomeQuery->toSql()}) as specialIncomeData"))
        ->mergeBindings(($specialIncomeQuery))
        // ->fromSub($specialIncomeData,'specialIncomeData')
        // ->leftJoinSub($incomeData,'incomeData',function ($join){
        ->leftJoin(DB::raw("({$incomeQuery->toSql()}) as incomeData"),function ($join){
            $join->on('specialIncomeData.year','=','incomeData.year')
                ->on('specialIncomeData.type','=','incomeData.type')
                ->on('specialIncomeData.detail','=','incomeData.detail');
        })->select(
            'specialIncomeData.year',
            'specialIncomeData.type',
            'specialIncomeData.detail',
            'specialIncomeData.special_income_quantity',
            DB::raw('IFNULL(incomeData.income_quantity,0) as income_quantity')
        );

        // $this->filter()->build($query);
        // Log::info(DB::getQueryLog());
        return $query->get();

    }

}