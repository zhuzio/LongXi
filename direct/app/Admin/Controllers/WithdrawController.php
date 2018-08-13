<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\GrantWithdrawAgree;
use App\Admin\Extensions\GrantWithdrawRefuse;
use App\Http\Controllers\Controller;
use App\Models\ApplyWithdraw;
use Carbon\Carbon;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class WithdrawController extends Controller {
	use ModelForm;

	public function waitCheck() {
		return Admin::content(function (Content $content) {

			$content->header('会员提现');
			$content->description('待处理');

			$content->body($this->grid(false));
		});
	}
	public function withdrawList() {
		return Admin::content(function (Content $content) {

			$content->header('会员提现');
			$content->description('已处理');
			$content->body($this->grid(true));
		});
	}
	protected function grid($check) {
		return Admin::grid(ApplyWithdraw::class, function (Grid $grid) use ($check) {
			if ($check) {
				$grid->disableActions();
				$grid->model()->where('status', '>', 0);

			}
			if (!$check) {
				$grid->model()->where('status', 0);
				$grid->actions(function ($actions) {
					$actions->disableDelete();
					$actions->disableEdit();
                    $actions->disableView();
					if ($actions->row->status == 0) {
						$actions->append(new GrantWithdrawAgree($actions->getKey(), '/admin/withdraw/confirmPayment'));
						$actions->append(new GrantWithdrawRefuse($actions->getKey(), '/admin/withdraw/refusePayment'));
					}
				});
			}
			$grid->disableCreateButton();
			$grid->disableRowSelector();
			$grid->model()->orderBy('id', 'desc');
			$filename = 'withdraw';
			$excel = new ExcelExpoter();
			$excel->setAttr(
				['id', '账号id', '联系方式', '服务中心', '服务中心电话','金额', '备注'],
				['id', 'user.account', 'user.phone', 'center.account', 'center.phone', 'real_money', 'mark'],
				$filename);
			$grid->exporter($excel);
			$grid->column('id', 'ID');
			$grid->column('user.account', '登录账号');
			$grid->column('user.phone', '联系方式');
			$grid->column('center.account', '服务中心');
            $grid->column('center.phone', '服务中心电话');
			if ($check) {
				$grid->column('status', '审核状态')->display(function ($status) {
					if ($status == 1) {
						return '<span class="label label-success">通过</span>';
					}
					if ($status == 2) {
						return '<span class="label label-success">通过</span>';
					}

				});
			}
			$grid->real_money('提现金额');
			$grid->mark('备注');

			$grid->column('user.level', '会员身份');

		});
	}
	public function confirmPayment() {
		$applyWithdraw = ApplyWithdraw::find(request('id'));
		$applyWithdraw->status = 1;
		$applyWithdraw->checked_at = Carbon::now();
		$applyWithdraw->save();
		return 'success';
	}
	public function refusePayment() {
		$applyWithdraw = ApplyWithdraw::find(request('id'));
		$applyWithdraw->status = 2;
		$applyWithdraw->checked_at = Carbon::now();
		$applyWithdraw->save();
		return 'success';
	}
}