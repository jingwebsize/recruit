<?php
namespace App\Admin\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Limitquery extends Repository
{

    //建一个数据仓库类，做复杂的sql语句，实现：Income表中按照年度year，类型type，明细detail, 归属单位unit进行分组求和得到数量，对比limit表中同一个年度下同一个类型和同一个明细，同一个归属单位下的数量（同样也对year，type，detail, unit进行分组并求和），将两个数据融合，最终得到一个包含year，type，detail，unit, 表1数量，表2数量的数组
    public function get(Grid\Model $model)
    {


        // //查询incomes表中按照年度year，类型type，明细detail，归属单位unit进行分组求和得到数量
        // $incomeData = DB::table('incomes')->select(DB::raw('year, type, detail, unit, sum(number) as income_quantity'))
        //     ->groupBy('year', 'type', 'detail', 'unit')
        //     ->get();
        
        // //查询limit表中按照年度year，类型type，明细detail，归属单位unit进行分组求和得到数量
        // $limitData = DB::table('limit')->select(DB::raw('year, type, detail, unit, sum(number) as limit_quantity'))
        //     ->groupBy('year', 'type', 'detail', 'unit')
        //     ->get();

        // //将两个表的数据融合，incomeData结果为主，相同year、type、detail、unit字段下，新增一列limit_quantity，值为limitData中对应的数量，若不存在则为0
        // $mergedData = $incomeData->map(function ($item) use ($limitData) {
        //     $limitQuantity = $limitData->where('year', $item->year)->where('type', $item->type)->where('detail', $item->detail)->where('unit', $item->unit)->first()->limit_quantity ?? 0;
        //     $item->limit_quantity = $limitQuantity;
        //     //mergedData按照income_quantity-limit_quantity剩余量由小到大排序
        //     $item->res = $item->income_quantity - $limitQuantity;
        
        //     return $item;
        // });

        // //mergedData按字段res从小到大排序
        // $mergedData = $mergedData->sortBy('res');

        // return $mergedData;
        // // return $specialIncomeData;

        // 获取当前页数
        $currentPage = $model->getCurrentPage();
        // 获取每页显示行数
        $perPage = $model->getPerPage();

        $start = ($currentPage - 1) * $perPage;

        $params = [
            'start' => $start,
            'perpage' => $perPage,
        ];

        $data = $this->getList($params); // 获取数据列表

        return $model->makePaginator(
            $data['total'] ?? 0, // 传入总记录数
            $data['subjects'] ?? [] // 传入数据二维数组
        );
    }

    public function getList(array $params){

        $perPage = $params['perpage'] ?? 20;
        $start = $params['start'] ?? 0;

        $type = request()->input('type');
        $year = request()->input('year');
        $detail = request()->input('detail');
        $unit = request()->input('unit');
        $enrollment_method = request()->input('enrollment_method');
        // dd($params);


        //查询Income表中按照年度year，类型type，明细detail，单位unit进行分组求和得到数量 
            $incomeQuery = DB::table('incomes')->select(DB::raw('year, type, detail, unit,enrollment_method, sum(number) as income_quantity'))
            ->groupBy('year', 'type', 'detail', 'unit','enrollment_method');
        
        //查询Limit表中按照年度year，类型type，明细detail，单位unit进进行分组求和得到数量

            $limitQuery = DB::table('limit')->select(DB::raw('year, type, detail, unit,enrollment_method, sum(number) as limit_quantity'))
            ->groupBy('year', 'type', 'detail', 'unit','enrollment_method');
        
        if ($type !== null) {
            $incomeQuery->whereIn('type', $type);
            $limitQuery->whereIn('type', $type);
        }
        if($year!== null){
            if ($year['start'] !== null) {
                $incomeQuery->where('year', '>=', $year['start']);
                $limitQuery->where('year', '>=', $year['start']);
            }
            if ($year['end'] !== null) {
                $incomeQuery->where('year', '<=', $year['end']);
                $limitQuery->where('year', '<=', $year['end']);
            }
        }
        if ($detail !== null) {
            $incomeQuery->whereIn('detail', $detail);
            $limitQuery->whereIn('detail', $detail);
        }
        if ($unit !== null) {
            $incomeQuery->where('unit', $unit);
            $limitQuery->where('unit', $unit);
        }
        if ($enrollment_method !== null) {
            $incomeQuery->where('enrollment_method', $enrollment_method);
            $limitQuery->where('enrollment_method', $enrollment_method);
        }


        // //将两个表融合
        // $mergedData = $specialIncomeData->merge($incomeData);
        //将两个表的数据融合，specialIncomeData结果为主，相同year、type、detail字段下，新增一列income_quantity，值为incomeData中对应的数量，若不存在则为0
        $query = DB::table(DB::raw("({$incomeQuery->toSql()}) as incomeData"))
        ->mergeBindings(($incomeQuery))
        // ->fromSub($specialIncomeData,'specialIncomeData')
        // ->leftJoinSub($incomeData,'incomeData',function ($join){
        ->leftJoin(DB::raw("({$limitQuery->toSql()}) as limitData"),function ($join){
            $join->on('incomeData.year','=','limitData.year')
                ->on('incomeData.type','=','limitData.type')
                ->on('incomeData.detail','=','limitData.detail')
                ->on('incomeData.unit','=','limitData.unit')
                ->on('incomeData.enrollment_method','=','limitData.enrollment_method');
        })->mergeBindings(($limitQuery))
        ->select(
            'incomeData.year',
            'incomeData.type',
            'incomeData.detail',
            'incomeData.unit',
            'incomeData.enrollment_method',
            'incomeData.income_quantity',
            DB::raw('IFNULL(limitData.limit_quantity,0) as limit_quantity'),
            DB::raw('incomeData.income_quantity-IFNULL(limitData.limit_quantity,0) as res')
        )->orderBy('year','desc')->orderBy('res','asc');

        // $query = DB::table('special_incomes')->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
        // ->groupBy('year', 'type', 'detail');


        $count= $query->count();
        // dd($perPage);
        // $query->orderBy('specialIncomeData.year','desc')->orderBy('specialIncomeData.type','asc')->orderBy('specialIncomeData.detail','asc');
        $list = $query->limit($perPage)->offset($start)->get()->toArray();
        // $this->filter()->build($query);
        // Log::info(DB::getQueryLog());
        // dd($list);
        return [
            'total' => $count,
            'subjects' => $list,
        ];
    }

}