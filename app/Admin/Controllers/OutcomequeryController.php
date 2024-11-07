<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Outcomequery;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Models\Outcome;
use Dcat\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;

class OutcomequeryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $title='统计分析';
    protected function grid()
    {
        return Grid::make(new Outcomequery(), function (Grid $grid) {

            $grid->column('year','年度')->sortable();
            $grid->column('type');
            // $grid->column('detail','对应明细');
            $grid->column('teacher','支出的教师姓名');
            $grid->column('unit','归属单位');
            $grid->column('admission_type','录取类别');
            $grid->column('enrollment_method','入学方式');
            $grid->column('profession','相应招生专业');


            //如果special_income_quantity值不大于income_quantity字体颜色为绿色，反之为红色
         
            $grid->column('limit_quantity','分配额度数量');
            $grid->column('outcome_quantity','已支出数')->display(function ($value){
                //显示收入汇总数量
                return $value;  
            })->modal(function ($modal){
                // 设置弹窗标题，显示'ID', '年度', '支出的教师姓名', '类型','明细','数量','归属单位','录取类别','入学方式','相应招生专业'的表格
                $modal->title('明细情况');

                $outcomes = Outcome::where('year',$this->year)->where('type',$this->type)->where('unit', $this->unit)->where('teacher',$this->teacher)->where('admission_type', $this->admission_type)->where('enrollment_method', $this->enrollment_method)->where('profession', $this->profession)->
                get(['id','year','type','student_id','unit','admission_type','enrollment_method','profession','student_name','actual_guidance_teacher','teacher'])->toArray();
                $titles=['ID', '年度', '类型', '考生身份证号（唯一）','归属单位','录取类别','入学方式','相应招生专业','学生姓名','实际指导老师','招生指标对应老师'];
                // $modal->table($titles,$comments);
                // 设置弹窗宽度1100px
                $modal->xl();
                return Table::make($titles, $outcomes);
                    
            });
            $grid->column('res','未支出数')->display(function ($value) {
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
            $grid->expandFilter();
            $grid->filter(function (Grid\Filter $filter) {
                // 更改为 panel 布局
                $filter->panel();
                $filter->between('year','年度')->year()->width(3);
                $filter->in('type', '类型')->multipleSelect(config('admin.types'))->width(3);
                // $filter->equal('type','类型');
                // $filter->in('detail', '明细')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                $filter->equal('unit','归属单位')->Select(config('admin.units'))->width(3);
                //录取类别
                $filter->equal('admission_type','录取类别')->Select(config('admin.admission_types'))->width(3);
                //录取方式
                $filter->equal('enrollment_method','入学方式')->Select(config('admin.enrollment_methods'))->width(3);

                //招生指标对应老师
                $filter->equal('teacher','支出老师姓名')->width(3);                
                $filter->equal('profession','专业')->width(3);
        
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
