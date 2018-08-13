<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplyWithdraw;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Http\Request;

//提现管理
class WithdrawController extends Controller {
	public function amount() {
		$user = request()->user;
		return [
			'code' => 200,
			'data' => $user->assets->electronic,
		];
	}
	/**
	 * 申请提现
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function applyWithdraw(Request $request) {
		$user = $request->user;
		if ($request->isMethod('get')) {
			return view('web.withdraw.applyWithdraw', ['data' => $user->assets->electronic]);
		}
		$user = $request->user;
		if ($request->money > $user->assets->electronic) {
			return [
				'code' => 500,
				'msg' => '提现不能超过现有余额',
			];
		}
		if ($request->money % 1000 != 0 || $request->money < 1000) {
			return [
				'code' => 500,
				'msg' => '请输入1000整数倍金额',
			];
		}
		if (!Hash::check($request->password, $user->payment_password)) {
			return [
				'code' => 500,
				'msg' => '支付密码错误',
			];
		}
		ApplyWithdraw::create([
			'uid' => $user->id,
			'money' => $request->money,
			'bank_id' => $request->bank_id,
			'mark' => $request->mark ?: '',
		]);
		DB::table('assets')
			->where('uid', $user->id)
			->decrement('electronic', $request->money);
		return [
			'code' => 200,
			'msg' => '提交成功，请等待审核',
		];
	}
	/**
	 * 提现列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function withdrawList(Request $request) {
		$list = ApplyWithdraw::with('bankCard')
			->where('uid', $request->user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->orderBy('created_at', 'desc')
            ->paginate(15);
		return view('web.withdraw.withdrawList', ['data' => $list]);
	}
}