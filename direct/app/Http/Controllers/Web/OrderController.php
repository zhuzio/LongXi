<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\PayOrder;
use App\Models\Product;
use App\Models\Users;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Validator;

//线上订单
class OrderController extends Controller {
	public function create(Request $request) {

		$pay_channel_arr = [
			'points' => PayOrder::SHOP_POINTS_PAY,
		];
		$validator = Validator::make(
			$request->all(),
			[
				'product_id' => 'required',
				'num' => 'required',
				'address_id' => 'required|numeric',
				'payChannel' => 'required',
			],
			[
				'product_id.required' => '请选择要购买的商品',
				'num.required' => '请选择要购买的商品数量',
				'address_id.required' => '请选择收货地址',
				'address_id.numeric' => '请选择收货地址',
				'payChannel.required' => '请选择支付方式',
			]
		);

		if ($validator->fails()) {
			$errors = $validator->errors()->all();

			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}
		$address_id = $request->address_id;
		$address = Address::find($address_id);
		if (!$address) {
			return [
				'code' => 500,
				'msg' => '请选择收货地址',
			];
		}
		$product = Product::find($request->product_id);
		$order = Order::create([
			'uid' => $request->user->id,
			'product_id' => $request->product_id,
			'total_price' => $product->price * $request->num,
			'total_num' => $request->num,
			'address_id' => $address_id,
			'remark' => $request->remark ?: '',
			'status' => Order::WAIT_PAY,
		]);
		$order->sn = date('YmdHis') . $order->id . rand(1000, 9999);
		$order->save();
		$pay_order = PayOrder::create([
			'order_sn' => $order->sn,
			'subject' => '商城购物',
			'total_fee' => $order->total_price,
			'pay_channel' => $request->payChannel ? $pay_channel_arr[$request->payChannel] : 0,
			'from' => PayOrder::FROM_ONLINE,
		]);

		$pay_order->pay_sn = $pay_order->createSN();
		$pay_order->save();

		return $this->handlePay($pay_order, $order, $request->user, $request);
	}
	//调起支付
	public function handlePay(PayOrder $pay_order, Order $order, Users $user, Request $request) {
		//购物积分支付
		if ($pay_order->pay_channel == PayOrder::SHOP_POINTS_PAY) {
			if ($user->assets->shop_points < $order->total_price) {
				return [
					'code' => 500,
					'msg' => '余额不足',
				];
			}

			if (!Hash::check($request->password, $request->user->payment_password)) {
				return [
					'code' => 500,
					'msg' => '支付密码错误',
				];
			}
			$user->assets->shop_points = $user->assets->shop_points - $order->total_price;
			$order->status = Order::PAYED;
			$order->pay_at = $pay_order->pay_at = Carbon::now();

			$user->save();
			$order->save();
			$pay_order->save();
			return [
				'code' => 200,
				'data' => '',
			];
		}

	}
	public function waitExpress(Request $request) {
		$list = Order::where('uid', $request->user->id)
			->with('payOrder')
			->whereNull('express_at')
			->whereNotNull('pay_at')
			->with('product')
			->get();

		return [
			'code' => 200,
			'data' => $list,
		];
	}
	public function waitpay(Request $request) {
		$list = Order::where('uid', $request->user->id)
			->whereNull('pay_at')
			->with('product')
			->get();
		return [
			'code' => 200,
			'data' => $list,
		];
	}

	public function waitreceive(Request $request) {
		$user = $request->user;
		$list = Order::where('uid', $user->id)
			->whereNotNull('pay_at')
			->where('pay_at', '>', 0)
			->where('express_at', '>', 0)
			->where('received_at', 0)
			->with('product')
			->get();

		return [
			'code' => 200,
			'data' => $list,
		];
	}

	public function completed(Request $request) {
		$user = $request->user;
		$list = Order::where('uid', $user->id)
			->with('product')
			->get();
		return [
			'code' => 200,
			'data' => $list,
		];
	}
	public function orderinfo(Request $request) {
		$order = Order::where('id', $request->order_id)
			->where('uid', $request->user->id)
			->with('product')
			->with('address')
			->first();

		return [
			'code' => 200,
			'data' => $order,
		];
	}
	public function payOrder(Request $request) {
		$order = Order::find($request->order_id);
        $pay_order = PayOrder::where('order_sn',$order->sn)->first();
		if (!$order || $order->uid != $request->user->id) {
			return [
				'code' => 500,
				'msg' => '没有订单信息',
			];
		}
		if ($order->pay_at) {
			return [
				'code' => 500,
				'msg' => '订单已支付',
			];
		}
        return $this->handlePay($pay_order, $order, $request->user,$request);
	}
	public function orderReceive(Request $request) {
		$order = Order::find($request->order_id);
		if (!$order || $order->uid != $request->user->id) {
			return [
				'code' => 500,
				'msg' => '没有订单信息',
			];
		}

		$order->received_at = Carbon::now();
		$order->save();
		return [
			'code' => 200,
			'msg' => '确认成功',
		];
	}

}
