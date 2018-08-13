<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeftRight;
use App\Models\ReportCenter;
use Illuminate\Http\Request;
use Redirect;
use Validator;
//申请报单中心
class ApplyController extends Controller {
	/**
	 * 计算旗下总人数
	 * 业绩满10万可申请报单中心
	 * @return [type] [description]
	 */
	public function sonNum($id, &$num = 0) {
		//获得当前用户左右区
		$user = LeftRight::where('uid', $id)->first();
		if ($user->left_id) {
			$num++;
			$this->sonNum($user->left_id, $num);
		}
		if ($user->right_id) {
			$num++;
			$this->sonNum($user->right_id, $num);
		}
		return $num;
	}
	public function apply(Request $request) {
	    if($request->isMethod('get')){
	        return view('web.apply.apply');
        }
		$sonNum = $this->sonNum($request->user->id);
		//旗下业绩
		$achievement = $sonNum * config('award.1.touch_back');
		if ($achievement < config('direct.achievement')) {
			return ['code' => 500, 'msg' => '业绩不足,不能申请'];
		}
		$apply = new ReportCenter;
		$apply->uid = $request->user->id;
		$apply->id_number = $request->user->id_number;
		$apply->save();
		return ['code' => 200, 'msg' => '申请成功，请等待审核'];
	}
}
