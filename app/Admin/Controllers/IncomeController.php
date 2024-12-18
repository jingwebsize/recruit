<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Income;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\IncomeImport;
use App\Admin\Actions\IncomeTemplate;
use App\Models\Tag;
use Dcat\Admin\Admin;

class IncomeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Income(), function (Grid $grid) {
            // tags是用数字来维护的，就需要做映射
            // $tags = Tag::all()->pluck('tag','id')->toArray();
            // dump($tags);
            $grid->withBorder();
            $grid->scrollbarX();
            $grid->column('id')->width(30);
            $grid->column('year')->width(60);
            //学校下达时间
            $grid->column('time')->width(100);
            $grid->column('type')->width(100);
            $grid->column('school_detail')->width(300);;
            $grid->column('number')->width(30);
            $grid->column('unit')->width(100);
            $grid->column('detail')->width(100);
            // $grid->column('detail')->using($tags);
            $grid->column('enrollment_method')->width(80);
            // $grid->column('check')->bool([1 => true, 0 => false]);;
            $grid->column('remark')->sortable()->width(200);
            $grid->column('operator');
            
            // $grid->column('created_at');
            
            //显示更新时间，时间格式为月-日
            $grid->column('updated_at')->sortable()->display(function ($value) {
                return date('Y-m-d', strtotime($value));
            });

            // $grid->column('updated_at')->sortable();

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
                    ->body(IncomeImport::make()));
                    // 下载导入模板
                    $tools->append(IncomeTemplate::make()->setKey('test_question'));

            });            
        
            // $grid->filter(function (Grid\Filter $filter) {
            //     // $filter->equal('id');
            //     $filter->between('year','年度')->year();
            //     $filter->in('type', '类型')->multipleSelect(config('admin.types'));
        
            // });
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->equal('time','下达时间')->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->equal('unit','归属单位')->Select(config('admin.units'))->width(3);
                $filter->in('detail', '归属单位下一级')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                $filter->equal('enrollment_method','入学方式')->Select(config('admin.enrollment_methods'))->width(3);
        
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
        return Show::make($id, new Income(), function (Show $show) {
            $show->field('id');
            $show->field('year');
            $show->field('type');
            $show->field('detail');
            $show->field('number');
            $show->field('unit');
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
        return Form::make(new Income(), function (Form $form) {
            $form->display('id');
            $form->text('year')->default(date('Y'));
            $form->text('time')->default(date('Y-m-d'));
            $form->select('type')->options(config('admin.types'));
            // $form->text('detail');
            //表单里的detail字段是从数据库tags表中tag字段里选择的

            $form->text('school_detail');
            $form->text('number');
            // $form->text('unit');
            $form->select('unit')->options(config('admin.units'));
            $form->select('detail')->options(function ($id) {
                $tags = \App\Models\Tag::pluck('tag', 'tag');
                return $tags;
            });
            $form->select('enrollment_method')->options(config('admin.enrollment_methods'));
            $form->switch('check')->default(1);
            $form->text('operator')->default(Admin::user()->name);
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
