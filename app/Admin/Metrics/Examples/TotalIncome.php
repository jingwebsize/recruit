<?php

namespace App\Admin\Metrics\Examples;


use Dcat\Admin\Widgets\Metrics\RadialBar;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Limit;

class TotalIncome extends RadialBar
{
    /**
     * 初始化卡片内容
     */
    protected function init()
    {
        parent::init();
        $this->title('收入');
        $this->height(300);
        $this->chartHeight(250);
        $this->chartLabels('分配完成度');
        // $this->chartLabels(['学硕', '学博', '专硕','专博']);
    }

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
  
        // 卡片内容
        // 调取Incomes数据表里的数据，按4个类型统计当前年度的数量，加总字段number的数量
        $incomes = Income::where('year', date('Y'))->get()->groupBy('type')->map(function ($item) {
            return $item->sum('number');
        });

        // $incomes = Income::where('year', date('Y'))->get()->groupBy('type')->map(function ($item) {
        //     return $item->count();
        // });
        // 调取Incomes数据表里的数据，按number字段加总得到总数
        $total = Income::where('year', date('Y'))->sum('number');

        // 图表数据
        $percent = Limit::where('year', date('Y'))->sum('number')/$total  * 100;

        $this->withContent($total);
        $this->withChart($percent);
        $this->withFooter(isset($incomes['学硕'])?$incomes['学硕']:0, isset($incomes['学博'])?$incomes['学博']:0, isset($incomes['专硕'])?$incomes['专硕']:0, isset($incomes['专博'])?$incomes['专博']:0);

        // // 图表数据
        // $this->withChart([70, 52, 26]);

        // // 总数
        // $this->chartTotal('Total', $total);
        // $this->withContent(23043, 14658, 4758);

        // // 图表数据
        // $this->withChart([70, 52, 26]);

        // // 总数
        // $this->chartTotal('Total', 344);
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return $this
     */
    public function withChart(int $data)
    {
        return $this->chart([
            'series' => [$data],
        ]);
    }

    /**
     * 卡片内容.
     *
     * @param int $finished
     * @param int $pending
     * @param int $rejected
     *
     * @return $this
     */
    public function withContent($total)
    {
        return $this->content(
            <<<HTML
            <div class="d-flex flex-column flex-wrap text-center">
                <h1 class="font-lg-2 mt-2 mb-0">{$total}</h1>
                <small>全部</small>
            </div>
HTML
        );
    }

    public function withFooter($type1, $type2, $type3, $type4)
    {
        return $this->footer(
            <<<HTML
<div class="d-flex justify-content-between p-1 ml-3 mr-3" style="padding-top: 0!important;">
    <div class="text-center">
        <p>学硕</p>
        <span class="font-lg-1">{$type1}</span>
    </div>
    <div class="text-center">
        <p>学博</p>
        <span class="font-lg-1">{$type2}</span>
    </div>
    <div class="text-center">
        <p>专硕</p>
        <span class="font-lg-1">{$type3}</span>
    </div>
    <div class="text-center">
        <p>专博</p>
        <span class="font-lg-1">{$type4}</span>
    </div>
</div>
HTML
        );
    }
}
