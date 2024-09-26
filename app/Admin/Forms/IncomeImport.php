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
        // 处理数据
        foreach ($data['Sheet1'] as $row) {
            //每行中明细是否存在在tag数组里，不存在就退出循环，报错
            if(!in_array($row['明细'], $tags)){
                return $this->response()->error('明细不存在');
            }
            // var_dump($row);
            // exit;
            // 创建数据，Income表
            Income::create([
                'year' => $row['年度'],
                // 'reason' => $row['收入原因'],
                // 'income_time' => $row['收入时间'],
                'type' => $row['类型'],
                'number' => $row['数量'],
                'detail' => $row['明细'],
                'unit' => $row['归属单位'],
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
