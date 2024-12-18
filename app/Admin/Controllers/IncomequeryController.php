<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Incomequery;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Models\Income;
use App\Models\Tag;
use Dcat\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;

class IncomequeryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $title='统计分析';
    protected function grid()
    {
        return Grid::make(new Incomequery(), function (Grid $grid) {
            // $grid->column('id')->sortable();
            // $results = Income::where('year',$this->year)->where('type',$this->type)->where('detail',$this->detail)->get(['id','year','type','detail','number','unit'])->toArray();
            // // $incomeData = Income::select(DB::raw('year, type, detail, sum(number) as income_quantity'))
            // //     ->groupBy('year', 'type', 'detail')
            // //     ->get();
            // dump($results);
            
            //grid数据按照sort，year排序
            // $grid->model()->orderBy('sort','asc');
            // $grid->column('sort');
            
            //获取查询参数
            // $type = request()->input('type');
            // $grid->model()->where('type',$type);

            $grid->column('year','年度');
            $grid->column('type');
            $grid->column('detail','归属单位下一级');
            // $grid->column('special_income_quantity','特殊收入数量');

            //如果special_income_quantity值不大于income_quantity字体颜色为绿色，反之为红色
         
            $grid->column('special_income_quantity','数量')->display(function ($value) {
                if($value > $this->income_quantity){
                    return "<span style='color:red;'>".$value."</span>";
                }else{
                    return "<span style='color:green;'>".$value."</span>";
                }
            });
            // $grid->column('special_income_quantity','数量');
            //这列显示incomes表的分类求和数量，增加弹出模态框超链接，点击后显示incomes表里在year、type、detail字段匹配的记录
            $grid->column('income_quantity','收入汇总数量')->display(function ($value){
                //显示收入汇总数量
                return $value;  
            })->modal(function ($modal){
                // 设置弹窗标题
                $modal->title('明细情况');
                $comments = Income::where('year',$this->year)->where('type',$this->type)->where('detail',$this->detail)->get(['id','year','type','detail','number','unit'])->toArray();
                $titles=['ID', '年度', '类型','明细','数量','归属单位'];
                // $modal->table($titles,$comments);
                return Table::make($titles, $comments);
                // return new Table(['ID', '年度', '类型','明细','数量','归属单位'], $comments->toArray());
                // // 设置弹窗内容
                // $modal->body(function ($content) {
                //     //显示表格
                //     $content->table(, function($table) {
                //         $table->id();
                //         $table->year();
                //         $table->type();
                //         $table->detail();
                //         $table->income_quantity();
                //     });
                    
            });
            // });
        

            // $grid->column('income_quantity','收入汇总数量');       
            // 默认展开过滤器
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->in('detail', '归属单位下一级')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                $filter->equal('enrollment_method','入学方式')->Select(config('admin.enrollment_methods'))->width(3);
        
            });
            // $grid->disablePagination();
            // $grid->disableDeleteButton();
            // $grid->disableEditButton();
            $grid->disableQuickEditButton();
            $grid->disableViewButton();
            //隐藏行操作那列
            $grid->disableActions();
            $grid->disableCreateButton();


        });
    }
}
