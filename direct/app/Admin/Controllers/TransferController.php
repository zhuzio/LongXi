<?php

namespace App\Admin\Controllers;

use App\Models\Transfers;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\ExcelExpoter;
class TransferController extends Controller
{
    use ModelForm;
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('线下订单');
            $content->description('会员间转账');

            $content->body($this->grid());
        });
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Transfers::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->disableActions();
            $filename = time();
            $excel = new ExcelExpoter();
            $excel->setAttr(
                ['id', '转出账号', '转出人姓名', '转入账号', '转入人姓名', '转入金额', '标注', '创建时间'],
                ['id', 'fromuser.account', 'fromuser.realname', 'touser.account', 'touser.realname', 'money', 'mark', 'created_at'],
                $filename);
            $grid->exporter($excel);
            $grid->column('fromuser.account', '转出账号');
            $grid->column('fromuser.realname', '转出人姓名');
            $grid->column('touser.account', '转入账号');
            $grid->column('touser.realname', '转入人姓名');
            $grid->column('money', '转入金额');
            $grid->mark('标注');
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->equal('fromuser.account', '转出账号');
                $filter->equal('touser.account', '转入账号');
                // 在这里添加字段过滤器
                $filter->equal('fromuser.realname', '转出人姓名');
                $filter->equal('touser.realname', '转入人姓名');
                $filter->equal('type', '转账类型')->select([
                    1 => '电子币转账',
                    2=> '电子币转购物积分',
                    3=> '购物积分互转',
                ]);
            });
        });
    }
}
