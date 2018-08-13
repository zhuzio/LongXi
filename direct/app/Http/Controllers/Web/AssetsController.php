<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
//个人资产信息
class AssetsController extends Controller {

	public function assets(Request $request) {
		return view('web.assets.assets', ['data' => $request->user->assets]);
	}
	/**
	 * 购物积分收支记录
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function shopPointsLog(Request $request) {
		$list = DB::table('shop_points_log')
			->where('uid', $request->user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->paginate(10);
		return view('web.assets.shopPointsLog', ['data' => $list]);
	}

	/**
	 * 动态收益
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function dynamicLog(Request $request) {
		$list = DB::table('dynamics')
			->where('uid', $request->user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->orderBy('id', 'desc')
			->paginate(10);
		return view('web.assets.dynamicLog', ['data' => $list]);
	}

	/**
	 * 静态收益
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function staticLog(Request $request) {
		$list = DB::table('statics')
			->where('uid', $request->user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->orderBy('id', 'desc')
			->paginate(10);
		return view('web.assets.staticLog', ['data' => $list]);
	}

}
