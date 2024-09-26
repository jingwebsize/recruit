<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use App\Models\Tag;
use App\Models\Limit;
use Dcat\Admin\Admin;

class LimitImport extends Form
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
        // 取config admin中的unit、type、admission_type、enrollment_method
        $types = config('admin.types');
        $admission_types = config('admin.admission_types');
        $enrollment_methods = config('admin.enrollment_methods');
        $units = config('admin.units');
        $count = 0;
        // 处理数据
        foreach ($data['Sheet1'] as $row) {
            $count++;
            //每行中明细是否存在在tag数组里，不存在就退出循环，报错
            if(!in_array($row['明细'], $tags)){
                return $this->response()->error('第'.$count.'行明细不存在');
            }
            //每行中类型是否存在在types数组里，不存在就退出循环，报错
            if(!in_array($row['类型'], $types)){
                return $this->response()->error('第'.$count.'行类型不存在');
            }
            //每行中归属单位是否存在在units数组里，不存在就退出循环，报错
            if(!in_array($row['归属单位'], $units)){
                return $this->response()->error('第'.$count.'行归属单位不存在');
            }
            //每行中录取类别是否存在在admission_types数组里，不存在就退出循环，报错
            if(!in_array($row['录取类别'], $admission_types)){
                return $this->response()->error('第'.$count.'行录取类别不存在');
            }
            //每行中入学方式是否存在在enrollment_methods数组里，不存在就退出循环，报错
            if(!in_array($row['入学方式'], $enrollment_methods)){
                return $this->response()->error('第'.$count.'行入学方式不存在');
            }
            // 创建数据，limit表，包含年度、支出到具体教师、类型、数量、明细、归属单位、录取类别、入学方式、明细、招生专业
            Limit::create([
                'year' => $row['年度'],
                'teacher' => $row['支出到具体教师'],
                'type' => $row['类型'],
                'number' => $row['数量'],
                'detail' => $row['明细'],
                'unit' => $row['归属单位'],
                'admission_type' => $row['录取类别'],
                'enrollment_method' => $row['入学方式'],
                'profession' => $row['招生专业'],
                'check_status' => 1,
                'operator' => Admin::user()->name,
                'remark' => $row['备注'],
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
