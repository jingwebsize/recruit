<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use App\Models\Tag;
use Dcat\Admin\Admin;

class TagImport extends Form
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
        // 
        foreach ($data['Sheet1'] as $row) {
            // exit;
            // 
            Tag::create([
                'tag' => $row['归属单位下一级（平台或专业）'],
                'display' => 1,
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
