<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Http\Controllers\Controller;
use App\Models\Dynamic;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

//用户动态收益
class DynamicController extends Controller {
	use ModelForm;

	/**
	 * 用户资产列表
	 *
	 * @return Content
	 */
	public function income() {
		return Admin::content(function (Content $content) {

			$content->header('动态收益');
			$content->description('列表');

			$content->body($this->grid());
		});
	}
	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Dynamic::class, function (Grid $grid) {
			$grid->disableActions();
			$grid->id('ID')->sortable();
            $grid->paginate(10);
			$grid->disableCreateButton();
			$filename = 'dynamic';
			$excel = new ExcelExpoter();

			$excel->setAttr(
				['id', '账号id', '姓名', '增值积分', '税', '重复消费', '收益类型', '发生在第几层', '标记'],
				['id', 'user.account', 'user.realname', 'added_points', 'tax', 'repeat_consum', 'type', 'floor', 'mark'],
				$filename);
			$grid->exporter($excel);
			$grid->column('user.account', '账户id');
			$grid->column('user.id_number', '身份证号');
			$grid->column('user.phone', '电话');
			$grid->column('user.realname', '姓名');
			$grid->column('deserve', '应得');
			$grid->column('tax', '扣税');
			$grid->column('repeat_consum', '重复消费');
            $grid->column('realize', '实得');
			$grid->column('type', '收益类型');
			$grid->column('floor', '发生在第几层');
			$grid->column('mark', '标记');
			$grid->filter(function ($filter) {
				// 去掉默认的id过滤器
				$filter->disableIdFilter();
				// 在这里添加字段过滤器
				$filter->equal('user.account', '账户id');
				$filter->gt('added_points', '获得收益大于');
			});
			$grid->created_at();
		});
	}
}
