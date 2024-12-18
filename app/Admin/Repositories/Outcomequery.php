<?php
namespace App\Admin\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Outcomequery extends Repository
{

    //建一个数据仓库类，做复杂的sql语句，实现：Income表中按照年度year，类型type，明细detail, 归属单位unit进行分组求和得到数量，对比limit表中同一个年度下同一个类型和同一个明细，同一个归属单位下的数量（同样也对year，type，detail, unit进行分组并求和），将两个数据融合，最终得到一个包含year，type，detail，unit, 表1数量，表2数量的数组
    public function get(Grid\Model $model)
    {
        
        // //查询limit表中按照年度year，类型type，归属单位unit，录取类别，入学方式，招生专业，教师，进行分组求和得到数量

        // $limitData = DB::table('limit')->select(DB::raw('year, type, teacher, unit, admission_type, enrollment_method, profession,sum(number) as limit_quantity'))
        //     ->groupBy('year', 'type', 'teacher','unit','admission_type', 'enrollment_method', 'profession')
        //     ->get();

        // //查询outcomes表中按照年度year，类型type，归属单位unit，录取类别，入学方式，招生专业，进行分组得到条目数量
        // $OutcomeData = DB::table('outcomes')->select(DB::raw('year, type, teacher, unit, admission_type, enrollment_method, profession, count(*) as outcome_quantity'))
        //     ->groupBy('year', 'type', 'teacher','unit','admission_type', 'enrollment_method', 'profession')
        //     ->get();

        // //将两个表的数据融合，limitData结果为主，相同year、type、detail、unit字段下，新增一列outcome_quantity，值为OutcomeData中对应的数量，若不存在则为0
        // $mergedData = $limitData->map(function ($item) use ($OutcomeData) {
        //     $outcome_quantity = $OutcomeData->where('year', $item->year)->where('type', $item->type)->where('teacher', $item->teacher)->where('admission_type', $item->admission_type)->where('unit', $item->unit)->where('admission_type', $item->admission_type)->where('enrollment_method', $item->enrollment_method)->where('profession', $item->profession)
        //     ->first()->outcome_quantity ?? 0;
        //     //mergedData按照剩余量由大到小排序
        //     $item->outcome_quantity = $outcome_quantity;
        //     $item->res = $item->limit_quantity - $outcome_quantity;
        
        //     return $item;
        // });

        // //mergedData按字段res从小到大排序
        // $mergedData = $mergedData->sortByDesc('res');

        // return $mergedData;
        // return $specialIncomeData;

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

        
        $year = request()->input('year');
        $type = request()->input('type');
        $teacher = request()->input('teacher');
        $profession = request()->input('profession');
        $unit = request()->input('unit');
        $detail = request()->input('detail');
        // $admission_type = request()->input('admission_type');
        $enrollment_method = request()->input('enrollment_method');
        

        // dd(request()->input());
        // dd($teacher);

        // 查询limit表，选择year, type, teacher, unit, admission_type, enrollment_method, profession字段，并计算number字段的总和，命名为limit_quantity
        $limitQuery = DB::table('limit')->select(DB::raw('year, type, teacher, unit, detail, enrollment_method, profession,sum(number) as limit_quantity'))
            ->groupBy('year', 'type', 'teacher','unit','detail', 'enrollment_method', 'profession');

        // 查询outcomes表，选择year、type、teacher、unit、admission_type、enrollment_method、profession字段，并计算每个组合的数量
        $OutcomeQuery = DB::table('outcomes')->select(DB::raw('year, type, teacher, unit, detail, enrollment_method, profession, count(*) as outcome_quantity'))
            ->groupBy('year', 'type', 'teacher','unit','detail', 'enrollment_method', 'profession');
        
        if ($type !== null) {
            $limitQuery->whereIn('type', $type);
            $OutcomeQuery->whereIn('type', $type);
        }
        if($year!== null){
            if ($year['start'] !== null) {
                $limitQuery->where('year', '>=', $year['start']);
                $OutcomeQuery->where('year', '>=', $year['start']);
            }
            if ($year['end'] !== null) {
                $limitQuery->where('year', '<=', $year['end']);
                $OutcomeQuery->where('year', '<=', $year['end']);
            }
        }
        if ($detail !== null) {
            $limitQuery->whereIn('detail', $detail);
            $OutcomeQuery->whereIn('detail', $detail);
        }
        if ($unit !== null) {
            $limitQuery->where('unit', $unit);
            $OutcomeQuery->where('unit', $unit);
        }
        if ($teacher !== null) {
            $limitQuery->where('teacher', $teacher);
            $OutcomeQuery->where('teacher', $teacher);
        }
        // if ($admission_type !== null) {
        //     $limitQuery->where('admission_type', $admission_type);
        //     $OutcomeQuery->where('admission_type', $admission_type);
        // }
        if ($enrollment_method !== null) {
            $limitQuery->where('enrollment_method', $enrollment_method);
            $OutcomeQuery->where('enrollment_method', $enrollment_method);
        }
        if ($profession !== null) {
            $limitQuery->where('profession', $profession);
            $OutcomeQuery->where('profession', $profession);
        }


        //将两个表融合

        $query = DB::table(DB::raw("({$limitQuery->toSql()}) as LimitData"))
        ->mergeBindings(($limitQuery))
        ->leftJoin(DB::raw("({$OutcomeQuery->toSql()}) as OutcomeData"),function ($join){
            $join->on('LimitData.year','=','OutcomeData.year')
                ->on('LimitData.type','=','OutcomeData.type')
                ->on('LimitData.detail','=','OutcomeData.detail')
                ->on('LimitData.unit','=','OutcomeData.unit')
                ->on('LimitData.teacher','=','OutcomeData.teacher')
                // ->on('LimitData.admission_type','=','OutcomeData.admission_type')
                ->on('LimitData.enrollment_method','=','OutcomeData.enrollment_method')
                ->on('LimitData.profession','=','OutcomeData.profession');
        })->mergeBindings(($OutcomeQuery))
        ->select(
            'LimitData.year',
            'LimitData.type',
            'LimitData.detail',
            'LimitData.unit',
            'LimitData.limit_quantity',
            'LimitData.teacher',
            // 'LimitData.admission_type',
            'LimitData.enrollment_method',
            'LimitData.profession',
            DB::raw('IFNULL(OutcomeData.outcome_quantity,0) as outcome_quantity'),
            DB::raw('LimitData.limit_quantity-IFNULL(OutcomeData.outcome_quantity,0) as res')
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