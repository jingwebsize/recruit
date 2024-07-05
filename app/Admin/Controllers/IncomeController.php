<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Income;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

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
            $grid->column('id')->sortable();
            $grid->column('year');
            $grid->column('type');
            $grid->column('detail');
            $grid->column('number');
            $grid->column('unit');
            $grid->column('check');
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
            $form->text('year');
            $form->text('type');
            $form->text('detail');
            $form->text('number');
            $form->text('unit');
            $form->text('check');
            $form->text('operator');
            $form->text('remark');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
