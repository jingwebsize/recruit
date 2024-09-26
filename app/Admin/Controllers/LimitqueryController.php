<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Limitquery;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Models\Limit;
use Dcat\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;
use App\Models\Tag;

class LimitqueryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $title='统计分析';
    protected function grid()
    {
        return Grid::make(new Limitquery(), function (Grid $grid) {
            // $grid->column('id')->sortable();
            // $results = Income::where('year',$this->year)->where('type',$this->type)->where('detail',$this->detail)->get(['id','year','type','detail','number','unit'])->toArray();
            // // $incomeData = Income::select(DB::raw('year, type, detail, sum(number) as income_quantity'))
            // //     ->groupBy('year', 'type', 'detail')
            // //     ->get();
            // dump($results);
            
            //grid数据按照sort，year排序
            // $grid->model()->orderBy('sort','asc');
            // $grid->column('sort');
            $grid->column('year','年度')->sortable();
            $grid->column('type');
            $grid->column('detail','对应明细');
            $grid->column('unit','归属单位');
            //如果special_income_quantity值不大于income_quantity字体颜色为绿色，反之为红色
         
            $grid->column('income_quantity','数量');
            $grid->column('limit_quantity','已分配额度')->display(function ($value){
                //显示收入汇总数量
                return $value;  
            })->modal(function ($modal){
                // 设置弹窗标题，显示'ID', '年度', '支出的教师姓名', '类型','明细','数量','归属单位','录取类别','入学方式','相应招生专业'的表格
                $modal->title('明细情况');

                $limits = Limit::where('year',$this->year)->where('type',$this->type)->where('detail',$this->detail)->where('unit', $this->unit)->get(['id','year','teacher','type','detail','number','unit','admission_type','enrollment_method','profession'])->toArray();
                $titles=['ID', '年度', '支出的教师姓名', '类型','明细','数量','归属单位','录取类别','入学方式','相应招生专业'];
                // $modal->table($titles,$comments);
                $modal->xl();
                return Table::make($titles, $limits);
                    
            })->width('150px');

            $grid->column('res','未分配数')->display(function ($value) {
                if($value < 0){
                    return "<span style='color:red;'>".$value."</span>";
                }else{
                    return "<span style='color:green;'>".$value."</span>";
                }
            });
            // });
        

            // $grid->column('income_quantity','收入汇总数量');       
        
            // $grid->filter(function (Grid\Filter $filter) {
            //     $filter->equal('id');
        
            // });
            // 默认展开过滤器
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                $filter->in('detail', '明细')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                $filter->equal('unit','归属单位')->Select(config('admin.units'))->width(3);
        
            });
            // $grid->disablePagination();
            // $grid->disableDeleteButton();
            // $grid->disableEditButton();
            $grid->disableQuickEditButton();
            $grid->disableViewButton();
            //隐藏行操作那列
            $grid->disableActions();
            $grid->disableCreateButton();
            //禁用导出按钮
            // $grid->export()->disableExportAll();


        });
    }
}
