<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use App\Models\Tag;
use App\Models\Outcome;
use Dcat\Admin\Admin;

class OutcomeImport extends Form
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
        // $admission_types = config('admin.admission_types');
        $enrollment_methods = config('admin.enrollment_methods');
        $units = config('admin.units');
        $count = 0;
        // 处理数据
        foreach ($data['Sheet1'] as $row) {
            $count++;
            //每行中明细是否存在在tag数组里，不存在就退出循环，报错
            if(!in_array($row['归属单位下一级'], $tags)){
                return $this->response()->error('第'.$count.'行归属单位下一级不存在');
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
            // if(!in_array($row['录取类别'], $admission_types)){
            //     return $this->response()->error('第'.$count.'行录取类别不存在');
            // }

            //每行中入学方式是否存在在enrollment_methods数组里，不存在就退出循环，报错
            if(!in_array($row['入学方式'], $enrollment_methods)){
                return $this->response()->error('第'.$count.'行入学方式不存在');
            }
            // 创建数据，Outcome表，包括年度、类型、考生编号（唯一）、录取类别、入学方式、归属单位、招生专业、学生姓名、实际指导老师、招生指标对应老师、状态、操作人
            // 跳过重复数据
        }
        foreach ($data['Sheet1'] as $row) {
            if(Outcome::where('student_id', $row['身份证号'])->exists()){
                continue;
            }
            Outcome::create([
                'year' => $row['年度'],
                'type' => $row['类型'],
                'student_id' => $row['身份证号'],
                'admission_batch' => $row['录取批次'],
                'admission_type' => $row['录取类别'],
                'enrollment_method' => $row['入学方式'],
                'unit' => $row['归属单位'],
                'detail' => $row['归属单位下一级'],
                'profession' => $row['招生专业'],
                'student_name' => $row['学生姓名'],
                'actual_guidance_teacher' => $row['实际指导老师'],
                'teacher' => $row['招生指标对应老师'],
                'check_status' => 1,
                'operator' => Admin::user()->name,
                'remark' => $row['备注'],
                //增加学生性别、学生高校、学校属性、学生专业、学生联系电话、学生邮箱
                'student_gender' => $row['学生性别'],
                'student_college' => $row['学生高校'],
                'school_attributes' => $row['学校属性'],
                'student_major' => $row['学生专业'],
                'student_phone' => $row['学生联系电话'],
                'student_email' => $row['学生邮箱'],
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
