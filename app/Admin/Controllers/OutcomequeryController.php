<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Outcomequery;
use App\Models\Tag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Models\Outcome;
use Dcat\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;
// use App\Admin\Actions\LimitExport;
use App\Admin\Forms\LimitExport;

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
            // dd($grid);

            $grid->column('year','年度')->sortable();
            $grid->column('type');
            $grid->column('teacher','招生指标对应老师');
            $grid->column('unit','归属单位');
            $grid->column('detail','归属单位下一级');
            $grid->column('profession','招生专业');
            // $grid->column('admission_type','录取类别');
            $grid->column('enrollment_method','入学方式');


            //如果special_income_quantity值不大于income_quantity字体颜色为绿色，反之为红色
         
            $grid->column('limit_quantity','分配额度数量')->sortable();
            $grid->column('outcome_quantity','已支出数')->display(function ($value){
                //显示收入汇总数量
                return $value;  
            })->modal(function ($modal){
                // 设置弹窗标题，显示'ID', '年度', '支出的教师姓名', '类型','明细','数量','归属单位','录取类别','入学方式','相应招生专业'的表格
                $modal->title('明细情况');
                $outcomes = Outcome::where('year',$this->year)->where('type',$this->type)->where('unit', $this->unit)->where('teacher',$this->teacher)->where('detail', $this->detail)->where('enrollment_method', $this->enrollment_method)->where('profession', $this->profession)->get();

                // //增加一个字段actionlink，值为编辑，点击后跳转到对应id的编辑页面
                
                // $link = url('admin/outcomes/'.$outcomes->id.'/edit');
                // $outcomes->actionlink= '<a target="_blank" href='.$link .'>编辑</a>';
                // $data =  $award->only(['id','year','type','student_id','admission_batch','admission_type','enrollment_method','unit','detail','profession','student_name','actual_guidance_teacher','teacher','actionlink']);

                //表格标题
                $titles=['ID','年度', '类型', '考生身份证号','录取批次','录取类别','入学方式','归属单位','归属单位下一级','招生专业','学生姓名','实际指导老师','招生指标对应老师','操作'];

                // 为每个 outcome 添加 actionlink
                $outcomes->transform(function ($outcome) {
                    $link = url('admin/outcomes/' . $outcome->id . '/edit');
                    $outcome->actionlink = '<a target="_blank" href="' . $link . '">编辑</a>';
                    return $outcome;
                });

                // 提取需要的字段
                $data = $outcomes->map(function ($outcome) {
                    return [
                        'id' => $outcome->id,
                        'year' => $outcome->year,
                        'type' => $outcome->type,
                        'student_id' => $outcome->student_id,
                        'admission_batch' => $outcome->admission_batch,
                        'admission_type' => $outcome->admission_type,
                        'enrollment_method' => $outcome->enrollment_method,
                        'unit' => $outcome->unit,
                        'detail' => $outcome->detail,
                        'profession' => $outcome->profession,
                        'student_name' => $outcome->student_name,
                        'actual_guidance_teacher' => $outcome->actual_guidance_teacher,
                        'teacher' => $outcome->teacher,
                        'actionlink' => $outcome->actionlink,
                    ];
                });
                
                // $modal->table($titles,$comments);
                // 设置弹窗宽度1100px
                
                $modal->xl();
                return Table::make($titles, $data->toArray());
                    
            });
            $grid->column('res','未支出数')->display(function ($value) {
                if($value < 0){
                    return "<span style='color:red;'>".$value."</span>";
                }else{
                    return "<span style='color:green;'>".$value."</span>";
                }
            })->sortable();
            
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
                
                $filter->equal('unit','归属单位')->Select(config('admin.units'))->width(3);
                $filter->in('detail', '归属单位下一级')->multipleSelect(Tag::pluck('tag','tag')->toArray())->width(3);
                //录取类别
                // $filter->equal('admission_type','录取类别')->Select(config('admin.admission_types'))->width(3);
                //录取方式
                $filter->equal('enrollment_method','入学方式')->Select(config('admin.enrollment_methods'))->width(3);

                //招生指标对应老师
                $filter->equal('teacher','招生指标对应老师')->width(3);                
                $filter->equal('profession','招生专业')->width(3);
        
            });
            // $grid->disablePagination();
            // $grid->disableDeleteButton();
            // $grid->disableEditButton();
            $grid->export(new LimitExport())->xlsx()
            ->disableExportSelectedRow()->disableExportCurrentPage();

            //增加一个导入excel文件的按钮
            // $grid->tools(function (Grid\Tools $tools) {
            //         $tools->append(LimitExport::make());
            // });   


            $grid->disableQuickEditButton();
            $grid->disableViewButton();
            //隐藏行操作那列
            $grid->disableActions();
            $grid->disableCreateButton();
            // dd($grid->get());
            // dd($grid->model()->getPaginator()->getOptions());

        // 添加汇总行
            $grid->header(function () use ($grid) {
                // 获取分页器对象
                $paginator = $grid->model()->paginator();
                // dd($paginator->getOptions());

                if (!$paginator) {
                    return ''; // 无分页数据时直接返回
                }

                // 从 options 中获取汇总数据
                $options = $paginator->getOptions();
                // dd($options ->toArray());
                $totals = $options['totals'] ?? [];

                return <<<HTML
                <div style="padding: 10px; background: #f8f8f8;">
                    <strong>总计：</strong>
                    分配数量: <strong>{$totals['count_limit_quantity']}</strong>, 
                    已支出:  <strong>{$totals['count_outcome_quantity']}</strong>, 
                    未支出:  <strong>{$totals['count_res']}</strong>
                </div>
                HTML;
            });

        });
    }
}
