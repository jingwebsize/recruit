<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Outcome;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

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
            $grid->column('id')->sortable();
            $grid->column('year');
            $grid->column('type');
            $grid->column('student_id');
            $grid->column('admission_category');
            $grid->column('enrollment_method');
            $grid->column('affiliation_unit');
            $grid->column('admission_major');
            $grid->column('student_name');
            $grid->column('actual_guidance_teacher');
            $grid->column('teacher');
            $grid->column('check_status');
            $grid->column('operator');
            $grid->column('remark');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
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
            $show->field('admission_category');
            $show->field('enrollment_method');
            $show->field('affiliation_unit');
            $show->field('admission_major');
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
            $form->text('year');
            $form->text('type');
            $form->text('student_id');
            $form->text('admission_category');
            $form->text('enrollment_method');
            $form->text('affiliation_unit');
            $form->text('admission_major');
            $form->text('student_name');
            $form->text('actual_guidance_teacher');
            $form->text('teacher');
            $form->text('check_status');
            $form->text('operator');
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
