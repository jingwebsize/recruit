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

class Incomequery extends Repository
// class Incomequery extends QueryBuilderRepository
// class Incomequery extends EloquentRepository
{


    //建一个数据仓库类，做复杂的sql语句，实现：Income表中按照年度year，类型type，明细detail进行分组求和得到数量，对比special_income表中同一个年度下同一个类型和同一个明细下的数量（同样也对year，type，detail进行分组并求和），将两个表融合，最终得到一个包含year，type，detail，表1数量，表2数量的数组
    public function get(Grid\Model $model)
    // public function get()
    {
        //获取查询条件
        // $query = $this->query();
        // $query = $model->newQuery();
        $special_incomequery= DB::table('special_incomes');
        $incomequery= DB::table('incomes');
        $type = request()->input('type');
        if ($type !== null) {

            $special_incomequery->where('type', $type);
            $incomequery->where('type', $type);
        }
        // //获取查询条件
        // $year = $model->filter()->input('year');
        // if ($year !== null) {
        //     $query->where('year', $year);
        // }



        // function scopeActive($query)
        // {
        //     $startDate= $model->filter()->input('year');
        //     if ($year !== null) {
        //         $query->whereBetween('created_at', [$startDate, $endDate]);
        //     }
    
        //     return $query->where('active', 1);
        // }
        //查询special_income表中按照年度year，类型type，明细detail进行分组求和得到数量 
        $specialIncomeData = $special_incomequery->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
            ->groupBy('year', 'type', 'detail')
            ->get();
        
        //查询Income表中按照年度year，类型type，明细detail进行分组求和得到数量
        $incomeData = $incomequery
            ->select(DB::raw('year, type, detail, sum(number) as income_quantity'))
            ->groupBy('year', 'type', 'detail')
            ->get();

        // //将两个表融合
        // $mergedData = $specialIncomeData->merge($incomeData);
        //将两个表的数据融合，specialIncomeData结果为主，相同year、type、detail字段下，新增一列income_quantity，值为incomeData中对应的数量，若不存在则为0
        $mergedData = $specialIncomeData->map(function ($item) use ($incomeData) {
            $incomeQuantity = $incomeData->where('year', $item->year)->where('type', $item->type)->where('detail', $item->detail)->first()->income_quantity ?? 0;
            $item->income_quantity = $incomeQuantity;
            //mergedData按照incomeQuantity-special_income_quantity由小到大排序
            $item->sort = $incomeQuantity - $item->special_income_quantity;
        
            return $item;
        });
        //mergedData按字段sort从小到大排序
        $mergedData = $mergedData->sortBy('sort');

        //应用过滤器
        // $query = $model->filter();
        // $mergedData = $mergedData->filter()->bulid($query);

        return $mergedData;
        // return $specialIncomeData;

    }

}