<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Limit;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Admin;
use App\Admin\Forms\LimitImport;
use App\Admin\Actions\LimitTemplate;

class LimitController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Limit(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('year');
            $grid->column('teacher');
            // $grid->column('time');
            $grid->column('type');
            $grid->column('admission_type');
            $grid->column('enrollment_method');
            $grid->column('detail');
            $grid->column('number');
            $grid->column('unit');
            $grid->column('profession');
            $grid->column('check_status')->bool([1 => true, 0 => false]);;
            $grid->column('operator');
            $grid->column('remark');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });

            //增加一个导入excel文件的按钮
            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(Modal::make()
                    // 大号弹窗
                    ->lg()
                    // 弹窗标题
                    ->title('上传文件')
                    // 按钮
                    ->button('<button class="btn btn-primary"><i class="feather icon-upload"></i> 导入数据</button>')
                    // 弹窗内容
                    ->body(LimitImport::make()));
                    // 下载导入模板
                    $tools->append(LimitTemplate::make()->setKey('test_question'));

            });   
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Limit(), function (Show $show) {
            $show->field('id');
            $show->field('year');
            $show->field('teacher');
            $show->field('time');
            $show->field('type');
            $show->field('admission_type');
            $show->field('enrollment_method');
            $show->field('detail');
            $show->field('number');
            $show->field('unit');
            $show->field('profession');
            $show->field('check_status');
            $show->field('operator');
            $show->field('remark');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Limit(), function (Form $form) {
            $form->display('id');
            // $form->text('year');
            $form->text('year')->default(date('Y'));
            $form->select('type')->options(config('admin.types'));
            $form->text('teacher');
            // $form->text('time');
            // $form->text('type');
            // $form->text('admission_type');
            // $form->text('enrollment_method');
            // $form->text('detail');

            $form->select('admission_type')->options(config('admin.admission_types'));
            $form->select('enrollment_method')->options(config('admin.enrollment_methods'));
            $form->select('detail')->options(function ($id) {
                // $tags = \App\Models\Tag::pluck('tag', 'id');
                $tags = \App\Models\Tag::pluck('tag', 'tag');
                return $tags;
            });
            $form->text('number');
            // $form->text('unit');
            $form->select('unit','归属单位')->options(config('admin.units'));
            $form->text('profession');
            // $form->text('check_status');
            // $form->text('operator');
            $form->switch('check_status')->default(1);
            $form->text('operator')->default(Admin::user()->name);
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
