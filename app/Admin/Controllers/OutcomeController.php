<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Outcome;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Admin;
use App\Admin\Forms\OutcomeImport;
use App\Admin\Actions\OutcomeTemplate;
use App\Models\Tag;

class OutcomeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Outcome(), function (Grid $grid) {
            $grid->withBorder();
            $grid->scrollbarX();
            $grid->column('id')->width(30);
            // $grid->number('id')->width(30);
            $grid->column('year')->width(60);
            $grid->column('type')->width(60);
            $grid->column('student_id')->width(160);
            $grid->column('admission_batch')->width(100);
            $grid->column('admission_type')->width(100);
            $grid->column('enrollment_method')->width(100);
            $grid->column('unit')->width(100);
            $grid->column('detail')->width(120);
            $grid->column('profession')->width(100);
            $grid->column('student_name')->width(80);
            $grid->column('actual_guidance_teacher')->width(80);
            $grid->column('teacher') ->width(100);
            // $grid->column('check_status');
            
            $grid->column('remark')->width(100);
            $grid->column('operator')->width(100);
            $grid->column('updated_at')->sortable()->display(function ($value) {
                return date('Y-m-d', strtotime($value));
            })->width(100);
            //增加录取批次、学生性别、学生高校、学校属性、学生专业、学生联系电话、学生邮箱
            $grid->column('student_gender');
            $grid->column('student_college');
            $grid->column('school_attributes');
            $grid->column('student_major');
            $grid->column('student_phone');
            $grid->column('student_email');

            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();
        
            // $grid->filter(function (Grid\Filter $filter) {
            //     $filter->equal('id');
        
            // });
            


            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->equal('unit','归属单位')->Select(config('admin.units'))->width(3);
                $filter->in('detail', '归属单位下一级')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                //录取类别
                $filter->equal('admission_type','录取类别')->Select(config('admin.admission_types'))->width(3);
                //录取方式
                $filter->equal('enrollment_method','入学方式')->Select(config('admin.enrollment_methods'))->width(3);
                //实际指导老师
                $filter->equal('actual_guidance_teacher','实际指导老师')->width(3);
                //招生指标对应老师
                $filter->equal('teacher','招生指标对应老师')->width(3);                
                $filter->equal('profession','专业')->width(3);
                $filter->equal('student_id','考生编号')->width(3);
                $filter->equal('student_name','考生姓名')->width(3);


        
            });
            $grid->export();
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
                    ->body(OutcomeImport::make()));
                    // 下载导入模板
                    $tools->append(OutcomeTemplate::make()->setKey('test_question'));

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
        return Show::make($id, new Outcome(), function (Show $show) {
            $show->field('id');
            $show->field('year');
            $show->field('type');
            $show->field('student_id');
            $show->field('admission_type');
            $show->field('enrollment_method');
            $show->field('unit');
            $show->field('profession');
            $show->field('student_name');
            $show->field('actual_guidance_teacher');
            $show->field('teacher');
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
        return Form::make(new Outcome(), function (Form $form) {
            $form->display('id');
            $form->text('year')->default(date('Y'));
            // $form->text('type');
            $form->select('type')->options(config('admin.types'));
            $form->text('student_id');
            // $form->text('enrollment_method');
            $form->text('admission_batch');
            $form->text('admission_type');
            // $form->select('admission_type')->options(config('admin.admission_types'));
            $form->select('enrollment_method')->options(config('admin.enrollment_methods'));
            // $form->text('unit');
            $form->select('unit','归属单位')->options(config('admin.units'));
            $form->select('detail')->options(function ($id) {
                $tags = \App\Models\Tag::pluck('tag', 'tag');
                return $tags;
            });
            $form->text('profession');
            $form->text('student_name');
            $form->text('actual_guidance_teacher');
            $form->text('teacher');
            // $form->text('check_status');
            // $form->text('operator');
            $form->switch('check_status')->default(1);
            $form->text('operator')->default(Admin::user()->name);
            $form->text('remark');

            //增加学生性别、学生高校、学校属性、学生专业、学生联系电话、学生邮箱
            $form->text('student_gender');
            $form->text('student_college');
            $form->text('school_attributes');
            $form->text('student_major');
            $form->text('student_phone');
            $form->text('student_email');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
