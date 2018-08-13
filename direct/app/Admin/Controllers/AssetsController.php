<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class AssetsController extends Controller {
	use ModelForm;

	/**
	 * 用户资产列表
	 *
	 * @return Content
	 */
	public function users() {
		return Admin::content(function (Content $content) {

			$content->header('用户资产');
			$content->description('列表');

			$content->body($this->grid());
		});
	}
	/**
	 * 用户动态收益统计
	 * @return [type] [description]
	 */
	public function dynamicIncome() {
		return Admin::content(function (Content $content) {

			$content->header('用户收益');
			$content->description('动态');

			$content->body($this->grid());
		});
	}
	/**
	 * 用户静态收益统计
	 * @return [type] [description]
	 */
	public function staticIncome() {
		return Admin::content(function (Content $content) {

			$content->header('用户收益');
			$content->description('动态');

			$content->body($this->grid());
		});
	}
	/**
	 * Edit interface.
	 *
	 * @param $id
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('header');
			$content->description('description');

			$content->body($this->form()->edit($id));
		});
	}

	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {
		return Admin::content(function (Content $content) {

			$content->header('header');
			$content->description('description');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Assets::class, function (Grid $grid) {
			$grid->disableActions();
			$grid->id('ID')->sortable();
			$grid->disableCreateButton();
			$filename = date('Ymd');
			$excel = new ExcelExpoter();
			$excel->setAttr(
				['id', '账号id', '姓名', '电子币', '广告积分', '增值积分','重消', '创建日期'],
				['id', 'user.account', 'user.realname', 'electronic', 'advert_points', 'added_points', 'repeat_consum','created_at'],
				$filename);
			$grid->exporter($excel);
			$grid->column('user.account', '账户id');
			$grid->column('user.id_number', '身份证号');
			$grid->column('user.phone', '电话');
			$grid->column('user.realname', '姓名');
			$grid->column('electronic', '电子币');
			$grid->column('added_points', '增值积分');
			$grid->column('advert_points', '广告积分');
			$grid->column('shop_points', '购物积分');
			$grid->column('repeat_consum', '重复消费');
			$grid->filter(function ($filter) {
				// 去掉默认的id过滤器
				$filter->disableIdFilter();
				// 在这里添加字段过滤器
				$filter->equal('user.account', '账户id');
				$filter->gt('repeat_consum', '重复消费大于');
			});
			$grid->created_at();
			$grid->updated_at();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Assets::class, function (Form $form) {

			$form->display('id', 'ID');

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
}
