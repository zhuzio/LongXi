<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transfers;
use Illuminate\Http\Request;
use Hash;
use App\Models\Users;
use Log;
use Input, Validator,  Session, Captcha;
use Carbon\Carbon;
//电子币转让(对冲，电子币->购物积分)
class TransferController extends Controller {
	/**
	 * 对冲
	 * 转让电子币
	 * @return [type] [description]
	 */
	public function electronicTransfer(Request $request) {
	    if($request->isMethod('get')){
	        return view('web.transfer.electronicTransfer',['data'=>$request->user]);
        }
		$user = $request->user;
	    if($request->to == $request->user->account){
            return ['code' => 500, 'msg' => '不能转给自己'];
        }
		$to = Users::with('assets')
            ->where('account', $request->to)
			->first();
		if (!$to) {
			return ['code' => 500, 'msg' => '转出的账户不存在'];
		}
        if (!Hash::check($request->password, $request->user->payment_password)) {
            return [
                'code' => 500,
                'msg' => '支付密码错误',
            ];
        }
        $rules = [
            "cpt" => 'required|captcha'
        ];
        $messages = [
            'cpt.required' => '请输入验证码',
            'cpt.captcha' => '验证码错误，请重试'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            $errors = $validator->errors()->all();
            return [
                'code' => 500,
                'msg' => $errors[0],
            ];
        }
		$money = $request->money;
		if ($user->assets->electronic < $money) {
			return ['code' => 500, 'msg' => '电子币不足'];
		}
		if(!$request->transfer_to){
            return ['code' => 500, 'msg' => '请选择转出类型'];
        }
		$info = "电子币转出\r\n";
        $info .= "转出金额为$money\r\n";
		if($request->transfer_to == 'shop_points'){
		    $info .= "电子币转出购物积分\r\n";
            $info .= $user->account."转出前电子币为".$user->assets->electronic."\r\n";
            $info .= $to->account."转入前购物积分为".$to->assets->shop_points."\r\n";
		    $to->assets->shop_points = $to->assets->shop_points + $money;
            $user->assets->electronic = $user->assets->electronic - $money;
            $to->assets->save();
            $user->assets->save();
            $info .= $user->account."转出后电子币为".$user->assets->electronic."\r\n";
            $info .= $to->account."转入后购物积分为".$to->assets->shop_points."\r\n";
            $type = Transfers::TRANSFER_TO_SHOPPOINTS;
        }
        if($request->transfer_to == 'electronic'){
            $info .= "电子币转出电子币\r\n";

            $info .= $user->account."转出前电子币为".$user->assets->electronic."\r\n";
            $info .= $to->account."转入前电子币为".$to->assets->electronic."\r\n";
            $to->assets->electronic = $to->assets->electronic + $money;
            $user->assets->electronic = $user->assets->electronic - $money;
            $to->assets->save();
            $user->assets->save();
            $info .= $user->account."转出后电子币为".$user->assets->electronic."\r\n";
            $info .= $to->account."转入后电子币为".$to->assets->electronic."\r\n";
            $type = Transfers::TRANSFER_TO_ELECTRONIC;
        }
        Log::info($info);
		Transfers::create([
			'from' => $user->id,
			'to' => $to->id,
			'money' => $money,
			'mark' => '电子币转出',
			'status' => 1,
            'type' => $type,
		]);
		return ['code'=>200,'msg'=>'转出成功'];
	}

    /**
     * 购物积分转出
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shopPointsTransfer(Request $request) {
        if($request->isMethod('get')){
            return view('web.transfer.shopPointsTransfer',['data'=>$request->user]);
        }
        $user = $request->user;
        if($request->to == $request->user->account){
            return ['code' => 500, 'msg' => '不能转给自己'];
        }
        $to = Users::with('assets')
            ->where('account', $request->to)
            ->first();
        if (!$to) {
            return ['code' => 500, 'msg' => '转出的账户不存在'];
        }
        if (!Hash::check($request->password, $request->user->payment_password)) {
            return [
                'code' => 500,
                'msg' => '支付密码错误',
            ];
        }
        $money = $request->money;
        if ($user->assets->shop_points < $money) {
            return ['code' => 500, 'msg' => '购物积分不足'];
        }
        $info = "购物积分转出\r\n";
        $info .= "转出金额为$money\r\n";
        $info .= $user->account."转出前购物积分为".$user->assets->shop_points."\r\n";
        $info .= $to->account."转入前购物积分为".$to->assets->shop_points."\r\n";
        $to->assets->shop_points = $to->assets->shop_points + $money;
        $user->assets->shop_points = $user->assets->shop_points - $money;
        $to->assets->save();
        $user->assets->save();
        $info .= $user->account."转出后购物积分为".$user->assets->shop_points."\r\n";
        $info .= $to->account."转入后购物积分为".$to->assets->shop_points."\r\n";
        Log::info($info);
        Transfers::create([
            'from' => $user->id,
            'to' => $to->id,
            'money' => $money,
            'mark' => '购物积分转出',
            'status' => 1,
            'type' => Transfers::TRANSFER_SHOPPOINTS,
        ]);
        return ['code'=>200,'msg'=>'转出成功'];
    }
	public function transferList(Request $request){
        $list = Transfers::with('touser')
            ->with('fromuser')
            ->where('from',$request->user->id)
            ->orWhere('to',$request->user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
            ->orderBy('id','desc')
            ->paginate(10);
        return view('web.transfer.transferList',['data'=>$list]);
    }
    public function checkUser(Request $request){
        $user = Users::where('account',$request->account)
            ->first();
        return ['code'=>200,'data'=>$user->realname];
    }
}
