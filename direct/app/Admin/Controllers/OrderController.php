<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
class OrderController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function waitExpress() {
		return Admin::content(function (Content $content) {

			$content->header('线上订单');
			$content->description('待支付');
			$content->body($this->grid('waitExpress'));
		});
	}
	public function waitreceive() {
		return Admin::content(function (Content $content) {

			$content->header('线上订单');
			$content->description('已发货');
			$content->body($this->grid('waitreceive'));
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

			$content->header('编辑订单信息');
			$content->description('');

			$content->body($this->form()->edit($id));
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid($way) {
		return Admin::grid(Order::class, function (Grid $grid) use ($way) {

			if ($way == 'waitExpress') {
				$grid->model()
                    ->whereNull('express_at')
                    ->whereNotNull('pay_at');
			}
			if ($way == 'waitreceive') {
				$grid->model()
					->whereNotNull('express_at')
					->whereNotNull('pay_at')
					->whereNull('received_at');
			}
			$grid->disableCreateButton();
			$grid->id('ID')->sortable();
			$grid->column('sn', '订单号');
			$grid->column('product.name', '商品名称');
			$grid->column('product.price', '商品价格');
			$grid->column('user.account', '账号');
			$grid->column('user.phone', '电话');
			$grid->column('user.realname', '真实姓名');
			$grid->created_at();
			$grid->updated_at();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append('<a href="/admin/redOrderDetail/'. $actions->getKey() .'"><i class="fa fa-eye"></i></a>');
            });
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Order::class, function (Form $form) {

			$form->display('sn', '订单号');
            $form->text('express_info', '快递信息')->rules('required');
            $form->datetime('express_at','发货时间');
			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
    public function redOrderDetail($id)
    {
        $order = Order::with('user')
            ->with('product')
            ->with('address')
            ->where('id', $id)
            ->first();
            if (!$order) {
                return;
            }
            $rows = [];
            $rows[] = [
                $order->sn,
                $order->user->account,
                $order->user->phone,
                $order->product->name,
                $order->product->price,
                $order->address->province.$order->address->city.$order->address->country.$order->address->detail,
                $order->express_info,
            ];

        return Admin::content(function (Content $content) use ($rows) {
            $content->header('订单详情');
            $content->row(function($row){
                $sendButton = <<<EOF
                <button class="btn btn-sm btn-primary" id="send-red">返回</button>
                <script>
                $('#send-red').on('click', function(){
                    window.history.back(-1); 
                  
                })
                </script>
EOF;
                $row->column(3, $sendButton);
            });
            $headers = ['订单号', '会员账号', '会员电话','商品名称', '商品价格','收货地址', '快递信息'];

            $content->row((new Table($headers, $rows)));
        });
    }
}
