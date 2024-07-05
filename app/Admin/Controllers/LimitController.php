<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Limit;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

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
            $grid->column('time');
            $grid->column('type');
            $grid->column('admission_type');
            $grid->column('enrollment_method');
            $grid->column('detail');
            $grid->column('number');
            $grid->column('affiliation_unit');
            $grid->column('profession');
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
            $show->field('affiliation_unit');
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
            $form->text('year');
            $form->text('teacher');
            $form->text('time');
            $form->text('type');
            $form->text('admission_type');
            $form->text('enrollment_method');
            $form->text('detail');
            $form->text('number');
            $form->text('affiliation_unit');
            $form->text('profession');
            $form->text('check_status');
            $form->text('operator');
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
