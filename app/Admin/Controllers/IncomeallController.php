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
use Illuminate\Support\Facades\DB;
use Dcat\Admin\Widgets\Table;

class IncomeallController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $title='总数统计';
    protected function grid()
    {
        return Grid::make(new Income(), function (Grid $grid) {
            // tags是用数字来维护的，就需要做映射
            // $tags = Tag::all()->pluck('tag','id')->toArray();
            // dump($tags);
            $grid->model()->select(DB::raw('year,time,type,sum(number) as total_number'))->groupBy('year','time','type');
            // $grid->column('id');
            $grid->column('year','年度');
            //学校下达时间
            $grid->column('time','下达时间');
            $grid->column('type');
            $grid->column('total_number','首批计划数量');
            $grid->column('expand','明细')->display(function()  {
                return '点击展开';
            })->expand(function($model){
                $detail = \App\Models\Income::where('year',$this->year)->where('time',$this->time)->where('type',$this->type)->get(['id','school_detail','number','unit','detail'])->toArray();
                //用table组件显示明细
                $titles = ['id','首批学校明细','首批分配数量','归属单位','归属单位下一级'];
                return Table::make($titles, $detail);

            });        
            //禁用编辑、显示、删除按钮
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableBatchActions();
            // $grid->filter(function (Grid\Filter $filter) {
            //     // $filter->equal('id');
            //     $filter->between('year','年度')->year();
            //     $filter->in('type', '类型')->multipleSelect(config('admin.types'));
        
            // });
            // $grid->withBorder();
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->equal('time', '下达时间')->width(3);
        
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
