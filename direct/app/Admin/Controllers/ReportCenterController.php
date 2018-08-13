<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\GrantAgree;
use App\Admin\Extensions\GrantRefuse;
use App\Http\Controllers\Controller;
use App\Models\ReportCenter;
use Carbon\Carbon;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReportCenterController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('header');
			$content->description('description');

			$content->body($this->grid());
		});
	}
	public function waitCheck() {
		return Admin::content(function (Content $content) {

			$content->header('待审核');
			$content->description('列表');

			$content->body($this->grid());
		});
	}
	public function agreeList() {
		return Admin::content(function (Content $content) {

			$content->header('审核通过');
			$content->description('列表');

			$content->body($this->grid(true));
		});
	}

	public function refuseList() {
		return Admin::content(function (Content $content) {

			$content->header('审核驳回');
			$content->description('列表');

			$content->body($this->grid(false, true));
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
	protected function grid($agree = false, $refuse = false) {
		return Admin::grid(ReportCenter::class, function (Grid $grid) use ($agree, $refuse) {

			$grid->actions(function ($actions) use ($agree, $refuse) {
				$actions->disableDelete();
				$actions->disableEdit();

			});
			$grid->disableCreateButton();
			$grid->disableRowSelector();
			$grid->column('user.id', 'ID');
			$grid->column('user.account', '账户id');
			$grid->column('user.realname', '姓名');
			$grid->column('user.level', '用户类型')->display(function ($type) {
				return $type == 1 ? '代理商' : '报单中心';
			});
			$grid->created_at('申请时间');
			if ($agree) {
				$grid->disableActions();
				$grid->model()->where('agree_at', '>', 0);
				$grid->column('agree_at', '通过时间');
			}
			if ($refuse) {
				$grid->disableActions();
				$grid->model()->where('refuse_at', '>', 0);
				$grid->column('refuse_at', '驳回时间');
			}
			if (!$agree && !$refuse) {

				$grid->model()->whereNull('agree_at')->whereNull('refuse_at');
				$grid->actions(function ($actions) {
					$actions->disableEdit();
					$actions->disableDelete();
                    $actions->disableView();
					// append一个操作
					$actions->append(new GrantAgree($actions->getKey(), '/admin/ReportCenter/agree'));
					$actions->append(new GrantRefuse($actions->getKey(), '/admin/ReportCenter/refuse'));

				});

			}

			$grid->filter(function ($filter) {

				// 去掉默认的id过滤器
				$filter->disableIdFilter();

				// 在这里添加字段过滤器
				$filter->equal('user.account', '账户id');
				$filter->between('created_at', '转出时间')->datetime();

			});

		});
	}

	public function agree() {
		$apply_report = ReportCenter::find(request('id'));
		$apply_report->agree_at = Carbon::now();
		$apply_report->save();
		$apply_report->user->level = 2;
		$apply_report->user->save();
		return 'success';
	}
	public function refuse() {
		$apply_report = ReportCenter::find(request('id'));
		$apply_report->refuse_at = Carbon::now();
		$apply_report->save();
		return 'success';
	}
}
