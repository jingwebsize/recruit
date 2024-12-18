<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Tag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\TagImport;
use App\Admin\Actions\TagTemplate;

class TagController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Tag(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('tag');
            // $grid->column('display');
            $grid->column('remark');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });

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
                    ->body(TagImport::make()));
                    // 下载导入模板
                    $tools->append(TagTemplate::make()->setKey('test_question'));

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
        return Show::make($id, new Tag(), function (Show $show) {
            $show->field('id');
            $show->field('tag');
            $show->field('display');
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
        return Form::make(new Tag(), function (Form $form) {
            $form->display('id');
            $form->text('tag');
            $form->switch('display')->default(1);
            //允许空值           
            $form->text('remark')->default('');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
