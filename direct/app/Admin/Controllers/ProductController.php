<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('商品');
			$content->description('商品列表');

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

			$content->header('商品');
			$content->description('编辑');

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

			$content->header('商品');
			$content->description('新增');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Product::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->column('name', '商品名称');
            $grid->column('pic', '商品图片')->display(function ($pic) {
                return "<img src='/upload/$pic' width='60' />";
            });
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
			$grid->column('price', '商品价格');
			$grid->column('num', '库存');
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
		return Admin::form(Product::class, function (Form $form) {

			$form->display('id', 'ID');
			$form->text('name', '商品名称')->rules('required');
			$form->select('category_id', '所属分类')->options(Category::selectOptions());
			$form->image('pic', '商品图片');
			$form->text('price', '商品价格')->rules('required');
			$form->text('num', '库存')->rules('required');
			$form->disableReset();
			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
}
