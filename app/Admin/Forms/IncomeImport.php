<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use App\Models\Tag;
use App\Models\Income;
use Dcat\Admin\Admin;

class IncomeImport extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        // dump($input);

        // return $this->response()->error('Your error message.');
        // 获取上传的文件路径
        $file_path = storage_path('app/public' . $input['import_file']);

        // 读取excel文件
        $data = Excel::import($file_path)->toArray();
        // [
        //     "Sheet1" => [
        //         2 => [
        //             "姓名" => "张三",
        //             "电话" => 123456789,
        //         ],
        //         3 => [
        //             "姓名" => "李四",
        //             "电话" => 987654321,
        //         ],
        //     ],
        // ]
        // 取数据库tags数据表的数据
        $tags =Tag::pluck('tag')->toArray();
        $types = config('admin.types'); 
        $enrollment_methods = config('admin.enrollment_methods');
        $units = config('admin.units');
        $count = 0;
        // 处理数据
        foreach ($data['Sheet1'] as $row) {
            $count++;
            if(!in_array($row['类型'], $types)){
                return $this->response()->error('第'.$count.'行类型不存在');
            }
            //每行中归属单位是否存在在units数组里，不存在就退出循环，报错
            if(!in_array($row['归属单位'], $units)){
                return $this->response()->error('第'.$count.'行归属单位不存在');
            }
            //每行中明细是否存在在tag数组里，不存在就退出循环，报错
            if(!in_array($row['归属单位下一级（平台或专业）'], $tags)){
                return $this->response()->error('第'.$count.'行归属单位下一级不存在');
            }
            //每行中入学方式是否存在在enrollment_methods数组里，不存在就退出循环，报错
            if(!in_array($row['入学方式'], $enrollment_methods)){
                return $this->response()->error('第'.$count.'行入学方式不存在');
            }
        }
        foreach ($data['Sheet1'] as $row) {
            // var_dump($row);
            // exit;
            // 创建数据，Income表
            Income::create([
                'year' => $row['年度'],
                // 'reason' => $row['收入原因'],
                // 'income_time' => $row['收入时间'],
                'time' => $row['学校计划下达时间'],
                'type' => $row['类型'],
                'school_detail' => $row['首批学校明细'],
                'number' => intval($row['首批分配数量']),
                'unit' => $row['归属单位'],
                'detail' => $row['归属单位下一级（平台或专业）'],
                'enrollment_method' => $row['入学方式'],
                'check' => 1,
                'operator' => Admin::user()->name,
                'remark' => $row['备注'],
                // 'created_at' => date('Y-m-d H:i:s'),
                // 'updated_at' => date('Y-m-d H:i:s'),
            ]);
          
            
        }

        // 入库
        //...

        return $this->response()->success('导入成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        // 禁用重置表单按钮
        $this->disableResetButton();

        // 文件上传
        $this->file('import_file', ' ')
            ->disk('public')
            ->accept('xls,xlsx')
            ->uniqueName()
            ->autoUpload()
            ->move('/import')
            ->help('支持xls,xlsx');
        
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    // public function default()
    // {
    //     return [
    //         'name'  => 'John Doe',
    //         'email' => 'John.Doe@gmail.com',
    //     ];
    // }
}
