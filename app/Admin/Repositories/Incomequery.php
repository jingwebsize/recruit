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

class Incomequery extends Repository
// class Incomequery extends QueryBuilderRepository
// class Incomequery extends EloquentRepository
{

    public function get(Grid\Model $model)

    {
        // 获取当前页数
        $currentPage = $model->getCurrentPage();
        // 获取每页显示行数
        $perPage = $model->getPerPage();

        $start = ($currentPage - 1) * $perPage;
        // 获取排序参数
        // $sort = $model->getSort();

        // 获取筛选条件
        // $type = $model->filter()->input('type.*');

        // dd($model->getSort());
        // dd($model->filter()->input());
        // dd(request()->all());

        $params = [
            'start' => $start,
            'perpage' => $perPage,
            // 'sort' => $sort,
            // 'type' => $type,
        ];

        $data = $this->getList($params); // 获取数据列表
        // dd($data['subjects']);

        return $model->makePaginator(
            $data['total'] ?? 0, // 传入总记录数
            $data['subjects'] ?? [] // 传入数据二维数组
        );

    }

    // public function getList(array $params){
    //     $perPage = $params['perpage'] ?? 20;
    //     $start = $params['start'] ?? 0;
    //     // $sort = $params['sort'];
    //     // $type = $params['type'];
    //     //查询special_income表中按照年度year，类型type，明细detail进行分组求和得到数量 
    //     $specialIncomeData = DB::table('special_incomes')->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
    //         ->groupBy('year', 'type', 'detail')
    //         ->limit($perPage)->offset($start)->get();
        
    //     //查询Income表中按照年度year，类型type，明细detail进行分组求和得到数量
    //     $incomeData = DB::table('incomes')
    //         ->select(DB::raw('year, type, detail, sum(number) as income_quantity'))
    //         ->groupBy('year', 'type', 'detail')
    //         ->get();

    //     // //将两个表融合
    //     // $mergedData = $specialIncomeData->merge($incomeData);
    //     //将两个表的数据融合，specialIncomeData结果为主，相同year、type、detail字段下，新增一列income_quantity，值为incomeData中对应的数量，若不存在则为0
    //     $mergedData = $specialIncomeData->map(function ($item) use ($incomeData) {
    //         $incomeQuantity = $incomeData->where('year', $item->year)->where('type', $item->type)->where('detail', $item->detail)->first()->income_quantity ?? 0;
    //         $item->income_quantity = $incomeQuantity;
    //         //mergedData按照incomeQuantity-special_income_quantity由小到大排序
    //         $item->sort = $incomeQuantity - $item->special_income_quantity;
        
    //         return $item;
    //     });
    //     //mergedData按字段sort从小到大排序
    //     $mergedData = $mergedData->sortBy('sort');
    //     return [
    //         'total' => $mergedData->count(),
    //         'subjects' => $mergedData,
    //     ];

    // }

    public function getList(array $params){

        $perPage = $params['perpage'] ?? 20;
        $start = $params['start'] ?? 0;


        // $specialIncomeQuery = DB::table('special_incomes')->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
        // ->groupBy('year', 'type', 'detail');

        // //查询Income表中按照年度year，类型type，明细detail进行分组求和得到数量
        // $incomeQuery = DB::table('incomes')
        //     ->select(DB::raw('year, type, detail, sum(number) as income_quantity'))
        //     ->groupBy('year', 'type', 'detail');

        $type = request()->input('type');
        $year = request()->input('year');
        $detail = request()->input('detail');
        // dd($params);


        //查询special_income表中按照年度year，类型type，明细detail进行分组求和得到数量 
            $specialIncomeQuery = DB::table('special_incomes')
            ->select(DB::raw('year, type, detail, sum(number) as special_income_quantity'))
            ->groupBy('year', 'type', 'detail');
        
        //查询Income表中按照年度year，类型type，明细detail进行分组求和得到数量
            $incomeQuery = DB::table('incomes')
            ->select(DB::raw('year, type, detail, sum(number) as income_quantity'))
            ->groupBy('year', 'type', 'detail');
        
        if ($type !== null) {
            $specialIncomeQuery->whereIn('type', $type);
            $incomeQuery->whereIn('type', $type);
        }
        if($year!== null){
            if ($year['start'] !== null) {
                $specialIncomeQuery->where('year', '>=', $year['start']);
                $incomeQuery->where('year', '>=', $year['start']);
            }
            if ($year['end'] !== null) {
                $specialIncomeQuery->where('year', '<=', $year['end']);
                $incomeQuery->where('year', '<=', $year['end']);
            }
        }
        if ($detail !== null) {
            $specialIncomeQuery->whereIn('detail', $detail);
            $incomeQuery->whereIn('detail', $detail);
        }


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
        })->mergeBindings(($incomeQuery))
        ->select(
            'specialIncomeData.year',
            'specialIncomeData.type',
            'specialIncomeData.detail',
            'specialIncomeData.special_income_quantity',
            DB::raw('IFNULL(incomeData.income_quantity,0) as income_quantity'),
            DB::raw('incomeData.income_quantity-IFNULL(incomeData.income_quantity,0) as res')
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