<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AutoReturn;
use App\Models\Config;
use App\Models\Dynamic;
use App\Models\LeftRight;
use App\Models\Statics;
use App\Models\Users;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Vinkla\Hashids\Facades\Hashids;

class CheckController extends Controller {
	/**
	 * 待审核列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function waitCheck(Request $request) {
		$users = Users::where('center_code', $request->user->account)
			->select('id', 'phone', 'account', 'realname', 'created_at','is_check')
			->orderBy('is_check', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);
		foreach ($users as $user) {
			if (!$user) {
				break;
			}
			$user->uid = Hashids::encode($user->id);
			unset($user->id);
		}
		return view('web.check.waitCheck', ['data' => $users]);
	}
	//通过code获取个人信息
	private function getUser($code) {
		return Users::where('account', $code)
			->with('contact')
			->first();
	}
	/**
	 * 通过注册申请
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function agree(Request $request) {
		if ($request->user->level != Users::CENTER_USER) {
			return ['code' => 500, 'msg' => '非报单中心不能审核'];
		}
		$config = [
		    '代理商'=>1,
            '服务中心'=>2,
            '经销商'=>3,
            '代理'=>4,
        ];
		$id = Hashids::decode($request->uid);
		$user = Users::where('id', $id[0])
			->with('assets')
			->first();
		if (!$user) {
			return ['code' => 500, 'msg' => '没有要通过的信息'];
		}
        $deduct_points = config('award.'.$config[$user->level].'.touch_back') + 640;
        if ($request->user->assets->electronic < $deduct_points) {
            return ['code' => 500, 'msg' => '电子币不足'];
        }
		if ($user->is_check > 0) {
			return ['code' => 500, 'msg' => '已经处理过了'];
		}
		$user->is_check = Users::CHECK_AGREE;
		$user->save();
        //扣除服务中心电子币
        $center_assets = $request->user->assets;
        Log::info("审核通过，开始扣除服务中心电子币" . $center_assets->electronic . "\r\n");
        $center_assets->electronic = $center_assets->electronic -
            $deduct_points;
        $center_assets->save();
        Log::info("扣除后服务中心电子币为" . $center_assets->electronic . "\r\n");
        DB::table('electronic_log')->insert([
            'uid' => $request->user->id,
            'points' => $deduct_points,
            'mark' => '审核会员扣除电子币',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
		$recommend_code = $user->recommend_code; //推荐人
		$contact_code = $user->contact_code; //安置人
		$recommend_user = self::getUser($recommend_code);
		$contact_user = self::getUser($contact_code);
		$center_user = $request->user;
		//添加个人关系表
		$lr = new LeftRight;
		$lr->uid = $user->id;
		$lr->pid = $contact_user->id;
		$lr->recommend_id = $recommend_user->id;
		$lr->floor = $contact_user->contact->floor + 1;
		$lr->link = $contact_user->contact->link . '-' . $user->id;
		$lr->center_id = $center_user->id;
		$lr->save();
		if ($user->place == 1) {
			$contact_user->contact->left_id = $user->id;
		} else {
			$contact_user->contact->right_id = $user->id;
		}
        $contact_user->contact->save();
        //如果是代理商（7882），添加静态定返
        $touch_back = config('award.'.$config[$user->level].'.touch_back');
        $advert_award = config('award.'.$config[$user->level].'.advert_award');
		if($user->level == Users::COMMON_USER){
            //给注册人添加广告积分定返信息表
            $auto_return = new AutoReturn;
            $auto_return->uid = $user->id;
            $auto_return->total_money = $touch_back;
            $auto_return->total_days = ceil($touch_back
                / $advert_award);
            $auto_return->daily_return = $advert_award;
            $auto_return->last_return = $touch_back - floor($touch_back / $advert_award) * ($advert_award);
            $auto_return->remaining_days = ceil($touch_back
                / $advert_award);
            $auto_return->save();
        }
        //查询推荐人是否回本
        //如果推荐人是（7882）
        //查询是否给推荐人定返翻倍
        if($recommend_user->level == Users::CENTER_USER ||
            $recommend_user->level == Users::COMMON_USER){

            $back = Dynamic::where('uid', $recommend_user->id)
                ->where('type', Dynamic::FROM_BACK_AWARD)
                ->first();
            //查询推荐人是否推荐
            $recommend = LeftRight::where('recommend_id', $recommend_user->id)
                ->first();
            $return = AutoReturn::where('uid', $recommend_user->id)
                ->first();
            //如果推荐了并且没有回本，可以定返翻倍
            $touch_back = config('award.'.$config[$recommend_user->level].'.touch_back');
            $advert_award = config('award.'.$config[$recommend_user->level].'.advert_award');
            if ($recommend && !$back) {
                //更新每日定返信息表
                $return->total_money = $return->total_money - $return->already_return;
                $return->total_days = ceil(($return->total_money - $return->already_return)
                    / ($advert_award * 2));
                Log::info(ceil(($return->total_money - $return->already_return)
                    / ($advert_award * 2)));
                $return->daily_return =
                    $advert_award * 2;
                $return->last_return = $return->total_money - floor(($return->total_money -
                            $return->already_return) / ($advert_award * 2)) * ($advert_award * 2);
                $return->already_return_days = 0;
                $return->remaining_days = ceil(($return->total_money - $return->already_return)
                    / ($advert_award * 2));
                $return->save();
            }
        }
        //审核代理商（7882），给服务中心发服务奖
        $floor = $user->contact->floor - $request->user->contact->floor;
        //给报单中心增加服务奖
        $service_award = $touch_back * 0.03;
        $this_points = $service_award; //扣除10%重消，5%税
        Dynamic::create([
            'uid' => $request->user->id,
            'deserve' => $service_award,
            'tax' => 0,
            'repeat_consum' => 0,
            'realize' => $this_points,
            'type' => Dynamic::FROM_SERVICE_AWARD,
            'floor' => $floor,
            'mark' => '第' . $floor . '层服务奖',
        ]);
        //增加增值积分
        $request->user->assets->added_points =
            $request->user->assets->added_points + $service_award * 0.85;
        $request->user->assets->save();
        event(new \App\Events\AutoDeduction($request->user));
		$users['register_user'] = $user;
		$users['recommend_user'] = $recommend_user;
		$users['contact_user'] = $contact_user;
		$users['center_user'] = $center_user;

		event(new \App\Events\CenterAgree($users));
		return ['code' => 200, 'msg' => '审核成功'];
	}
	public function todayIncome($user) {
		$money1 = Dynamic::where('uid', $user->id)
			->where('created_at', '>=', Carbon::today())
			->where('created_at', '<', Carbon::tomorrow())
			->sum('realize');
		$money2 = Statics::where('uid', $user->id)
			->where('created_at', '>=', Carbon::today())
			->where('created_at', '<', Carbon::tomorrow())
			->sum('realize');
		return $money1 + $money2;
	}
    public function checkLog(Request $request) {
        $list = DB::table('electronic_log')
            ->where('id',$request->user->id)
            ->paginate(15);
        return view('web.check.checkLog', ['data' => $list]);
    }
	/**
	 * 拒绝注册申请
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function refuse(Request $request) {
		$id = Hashids::decode($request->uid);
		$user = Users::find($id[0]);
		$user->is_check = Users::CHECK_REFUSE;
        $user->place = null;
		$user->save();
		return ['code' => 200, 'msg' => '操作成功'];
	}
}