<?php

namespace App\Admin\Forms;


// use Dcat\Admin\Admin;

use Dcat\EasyExcel\Excel;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use App\Admin\Repositories\Outcomequery; // 你的自定义 Repository

class LimitExport extends AbstractExporter
{

    public function export()
    {
        // 获取全部数据（忽略分页）
        $data = new Outcomequery();
        // dd($raw);
        $all = $data->getAllData();
        // dd(gettype($all));
        // $data001=array(['id' => 1, 'name' => 'Brakus', 'email' => 'treutel@eg.com', 'created_at' => '...'], ['id' => 2, 'name' => 'Klein', 'email' => 'koepp@eg.com', 'created_at' => '...']);
        //建立一个标题数组
        $headings = ['year'=>'年度', 'type'=>'类型', 'teacher'=>'招生指标对应老师', 'unit'=>'归属单位', 'detail'=>'归属单位下一级', 'profession'=>'招生专业', 'enrollment_method'=>'入学方式', 'limit_quantity'=>'分配额度数量', 'outcome_quantity'=>'已支出数', 'res'=>'未支出数'];

        // 使用 EasyExcel 导出数据$data(数组)，并以xlsx格式保存到本地
        // Excel::export(array_values($all), $heading)->download('AllData.csv');
        // $exporter = Excel::export($all);
        // dd(Excel::export($all)->csv()->raw());
        // return Excel::export($all)->headings($headings)->store('users.xlsx');
        // $all = [
        //     [
        //         'year' => '2022',
        //         'type' => '本科',
        //         'teacher' => '张三',
        //         'unit' => '计算机学院',
        //         'detail' => '计算机科学与技术',
        //         'profession' => '计算机科学与技术',
        //         'enrollment_method' => '统考',
        //         'limit_quantity' => '100',
        //         'outcome_quantity' => '50',
        //         'res' => '50',
        //     ],
        //     [
        //         'year' => '2022',
        //         'type' => '本科',
        //         'teacher' => '李四',
        //         'unit' => '计算机学院',
        //         'detail' => '计算机科学与技术',
        //         'profession' => '计算机科学与技术',
        //         'enrollment_method' => '统考',
        //         'limit_quantity' => '100',
        //         'outcome_quantity' => '50',
        //         'res' => '50',
        //     ],
        
        // ];

        // dd($all);
        // var_dump($all);
        // exit;

        // 转换为数组
        // foreach ($all as $key => $res) {
        //     $resArray[$key] = $res->toArray();
        // }


        // var_dump($all);
        // exit;

        return Excel::export($all)->headings($headings)->download('export_data.xlsx');

    }
}