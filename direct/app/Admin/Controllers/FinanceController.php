<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\Finance;
use App\Models\Users;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

//系统转出电子币
class FinanceController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('电子币');
			$content->description('转出');

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

			$content->header('电子币');
			$content->description('新增转出');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Finance::class, function (Grid $grid) {
			$grid->id('ID')->sortable();
			$grid->model()->orderBy('id', 'desc');
			$grid->column('money', '转出金额')->display(function ($money) {
				return $money . '元';
			});
			$grid->disableActions();
			$grid->column('user.account', '账户id');
            $grid->column('user.phone', '电话');
			$grid->column('user.realname', '姓名');
            $grid->column('type', '类型')->display(function($type){
                return $type == 1 ? '购买电子币':'电子币提现';
            });
			$grid->filter(function ($filter) {
				// 去掉默认的id过滤器
				$filter->disableIdFilter();
				// 在这里添加字段过滤器
				$filter->equal('user.account', '账户id');
			});
			$grid->created_at('购买时间');
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Finance::class, function (Form $form) {

			$form->display('id', 'ID');
			$form->text('to', '转出的账户id')->rules('required');
			$form->text('money', '转出金额')->rules('required');
            $form->radio('type', '转出类型')->options([
                1 => '购买电子币',
                2=> '电子币提现',
            ]);
			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
			$form->saving(function (Form $form) {
				$user = Users::where('account', $form->to)
					->first();
				if (!$user) {
					$error = new MessageBag([
						'title' => '账户不存在',
						'message' => '',
					]);
					return back()->with(compact('error'));
				}
				if (($form->money % 100) !== 0) {
					$error = new MessageBag([
						'title' => '金额为100的整数倍',
						'message' => '',
					]);
					return back()->with(compact('error'));
				}
			});
			$form->saved(function (Form $form) {
				$user = Users::where('account', $form->to)
					->with('assets')
					->first();
				$user->assets->electronic = $user->assets->electronic + $form->money;
				$user->assets->save();
			});
		});
	}
}
