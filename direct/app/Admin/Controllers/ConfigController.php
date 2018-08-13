<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ConfigController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('配置');
			$content->description('列表');

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

			$content->header('修改配置');
			$content->description('');

			$content->body($this->editForm()->edit($id));
		});
	}

	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {
		return Admin::content(function (Content $content) {

			$content->header('新增配置');
			$content->description('');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Config::class, function (Grid $grid) {
			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->disableRowSelector();
			$grid->disableExport();
			$grid->disableFilter();
			$grid->column('desc', '配置项');
			$grid->column('value', '值')->editable();
			$grid->created_at('创建时间');
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Config::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->text('key', '配置键值')->placeholder('英文输入')->help('请勿随意修改键值');
			$form->text('desc', '配置名称');
			$form->text('value', '配置值');
		});
	}

	protected function editForm() {
		return Admin::form(Config::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->display('key', '配置键值');
			$form->text('desc', '配置名称');
			$form->text('value', '配置值');
		});
	}
}
