<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\SpecialIncome;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\SincomeImport;
use App\Admin\Actions\SincomeTemplate;
use App\Models\Tag;

class SpecialIncomeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SpecialIncome(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('year');
            $grid->column('reason');
            //$grid->column('income_time');
            $grid->column('type');
            $grid->column('number');
            $grid->column('detail');
            $grid->column('remark');
            $grid->column('check')->bool([1 => true, 0 => false]);
            $grid->column('operator');
            $grid->column('created_at')->display(function($created_at){
                return date('Y-m-d', strtotime($created_at));
            });
            $grid->column('updated_at')->sortable()->display(function($updated_at){
                return date('Y-m-d', strtotime($updated_at));
            });
            
            $grid->export()->xlsx()->filename('特殊收入');
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
                    ->body(SincomeImport::make()));
                    // 下载导入模板
                    $tools->append(SincomeTemplate::make()->setKey('test_question'));

            });
        
            $grid->disableViewButton();
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->in('detail', '归属单位下一级')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
        
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
        return Show::make($id, new SpecialIncome(), function (Show $show) {
            $show->field('id');
            $show->field('year');
            $show->field('reason');
            $show->field('income_time');
            $show->field('type');
            $show->field('number');
            $show->field('detail');
            $show->field('check');
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
        return Form::make(new SpecialIncome(), function (Form $form) {
            $form->display('id');
            //默认调取当前年度
            $form->text('year')->default(date('Y'));
            $form->text('reason');
            // $form->text('income_time');
            // 选择config admin中的types值
            $form->select('type')->options(config('admin.types'));
            // $form->text('type');
            $form->text('number');
            // $form->text('detail');
            $form->select('detail')->options(function ($id) {
                // $tags = \App\Models\Tag::pluck('tag', 'id');
                $tags = \App\Models\Tag::pluck('tag', 'tag');
                return $tags;
            });
            $form->switch('check')->default(1);
            $form->text('operator')->default(Admin::user()->name);
            //$form->disable('operator')->auth();
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
