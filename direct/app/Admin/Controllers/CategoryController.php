<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class CategoryController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {
			$content->header('分类');
			$content->description('列表');

			$content->row(function (Row $row) {
				$row->column(6, $this->treeView()->render());

				$row->column(6, function (Column $column) {
					$form = new \Encore\Admin\Widgets\Form();
					$form->action(admin_url('categories'));

					$form->select('pid', '父分类')->options(Category::selectOptions());
					$form->text('title', '分类标题')->rules('required');
					$form->image('icon', '分类图标')->rules('required');

					$column->append((new Box(trans('admin.new'), $form))->style('success'));
				});
			});
		});
	}

	/**
	 * Redirect to edit page.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function show($id) {
		return redirect()->action(
			'\App\Admin\Controllers\CategoryController@edit', ['id' => $id]
		);
	}

	/**
	 * @return \Encore\Admin\Tree
	 */
	protected function treeView() {
		return Category::tree(function (Tree $tree) {
			$tree->disableCreate();

			$tree->branch(function ($branch) {
				$payload = "<strong>{$branch['title']}</strong>";

				return $payload;
			});
		});
	}

	/**
	 * Edit interface.
	 *
	 * @param string $id
	 *
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {
			$content->header('分类');
			$content->description('编辑');

			$content->row($this->form()->edit($id));
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	public function form() {
		return Category::form(function (Form $form) {
			$form->display('id', 'ID');

			$form->select('pid', '父分类')->options(Category::selectOptions());
			$form->text('title', '分类标题')->rules('required');
			$form->image('icon', '分类图标');
		});
	}

}
