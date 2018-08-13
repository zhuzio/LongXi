<?php

namespace App\Listeners;

use App\Events\CenterAgree;
use App\Models\AutoReturn;
use App\Models\Config;
use App\Models\Dynamic;
use App\Models\LeftRight;
use App\Models\Statics;
use App\Models\Touchs;
use App\Models\Users;
use Carbon\Carbon;
use DB;
use Log;

class AddPoints {

    protected $config;
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->config = [
            '代理商'=>1,
            '服务中心'=>2,
            '经销商'=>3,
            '代理'=>4,
        ];
	}
	/**
	 * Handle the event.
	 *
	 * @param  CenterAgree  $event
	 * @return void
	 */
	public function handle(CenterAgree $event) {
		$users = $event->users;
		$register_user = $users['register_user'];
		if (!$register_user) {
			return;
		}
		$recommend_user = $users['recommend_user'];
		if (!$recommend_user) {
			return;
		}
		$contact_user = $users['contact_user'];
		if (!$contact_user) {
			return;
		}
		$center_user = $users['center_user'];
		if (!$center_user) {
			return;
		}
		//回本
		$this->handleBack($register_user, $contact_user);
		//层奖
		$this->handleFloor($register_user);
	}
	public function handleBack($register_user, $contact_user) {
        Log::info($contact_user->account . "计算回本\r\n");
        Log::info("用户类型".$contact_user->level."\r\n");
		$back_user = LeftRight::where('pid', $contact_user->id)
			->where('uid', '<>', $register_user->id)
			->first();
		if (!$back_user) {
            Log::info($contact_user->account . "不存在一碰回本\r\n");
			return;
		}
		$todayIncome = $this->todayIncome($contact_user);
		Log::info($contact_user->account . "计算当天收益$todayIncome\r\n");
        $floor = $register_user->contact->floor -
            $contact_user->contact->floor;
		//如果是经销商或代理
		if($contact_user->level == Users::DEALER_USER ||
            $contact_user->level == Users::PROXY_USER){
            $touch_back = config('award.'.$this->config[$contact_user->level].'.floor_award');
            $limit = Config::where('key', 'proxy_limit')->first(['value']);
            $limit = $limit->value;
            $type = Dynamic::FROM_FLOOR_AWARD;
            $mark = '第' . $floor . '层层奖';
        }
        //如果是代理商或服务中心
        if($contact_user->level == Users::CENTER_USER ||
            $contact_user->level == Users::COMMON_USER){
            $touch_back = config('award.'.$this->config[$contact_user->level].'.touch_back');
            $limit = Config::where('key', 'agent_limit')->first(['value']);
            $limit = $limit->value;
            $type = Dynamic::FROM_BACK_AWARD;
            $mark = '第' . $floor . '层回本';
        }
		$this_points = $touch_back * 0.85; //扣除10%重消，5%税
        Log::info('$this_points:'.$this_points."\r\n");
		if ($todayIncome >= $limit) {
			return;
		}
		//如果当天收益<封顶值
        //如果当天收益 + 本次收益 > 封顶值
		if ($todayIncome < $limit) {
			if ($todayIncome + $this_points > $limit) {
				$this_points = $limit - $todayIncome;
				$touch_back = $this_points / 0.85;
			}
		}
		Dynamic::create([
			'uid' => $contact_user->id,
			'deserve' => $touch_back,
			'tax' => $touch_back * 0.05,
			'repeat_consum' => $touch_back * 0.1,
			'realize' => $this_points,
			'type' => $type,
			'floor' => $floor,
			'mark' => $mark,
		]);
		$original_amount = $contact_user->assets->added_points;
		$contact_user->assets->added_points =
		$contact_user->assets->added_points + $this_points;
        $contact_user->assets->added_points =
            $contact_user->assets->added_points + $this_points;
        $contact_user->assets->repeat_consum =
            $contact_user->assets->repeat_consum + $touch_back * 0.1;
		$contact_user->assets->save();
		$current_amount = $contact_user->assets->added_points;
		//添加碰对信息表***************
		$touch = new Touchs;
		$touch->uid = $contact_user->id;
		$touch->left_id = $register_user->id;
		$touch->original_amount = $original_amount;
		$touch->flowing = '+' . $touch_back;
		$touch->current_amount = $current_amount;
		$touch->right_id = $back_user->uid;
		$touch->touch_type = Touchs::FROM_BACK;
		$touch->save();
        //如果是代理商或服务中心
        if($contact_user->level == Users::CENTER_USER ||
            $contact_user->level == Users::COMMON_USER){
            $return = AutoReturn::where('uid', $contact_user->id)
                ->first();
            $return->remaining_days = 0;
            $return->save();
            Log::info("设置remaining_days = 0，表示已经回本\r\n");
            $already_return = DB::table('statics')
                ->where('uid',$contact_user->id)
                ->sum('realize');
            Log::info($contact_user->account."累计静态定返：$already_return\r\n");
            Log::info($contact_user->account."扣除静态定返前增值积分余额：".$contact_user->assets->added_points."\r\n");
            $contact_user->assets->added_points =
                $contact_user->assets->added_points - $already_return;
            $contact_user->assets->save();
            Log::info("扣除静态定返后增值积分余额：".$contact_user->assets->added_points."\r\n");

        }
		event(new \App\Events\AutoDeduction($contact_user));
	}
	public function todayIncome($user) {
		Log::info(Carbon::today());
		$money1 = DB::table('dynamics')
			->where('uid', $user->id)
            ->where('type', '<>',Dynamic::FROM_SERVICE_AWARD)
			->where('created_at', '>=', Carbon::today())
			->where('created_at', '<', Carbon::tomorrow())
			->sum('realize');
		Log::info(json_encode($money1));
		$money2 = DB::table('statics')
			->where('uid', $user->id)
			->where('created_at', '>=', Carbon::today())
			->where('created_at', '<', Carbon::tomorrow())
			->sum('realize');
		Log::info(json_encode($money2));
		return $money1 + $money2;
	}
	public function handleFloor($register_user) {
		$link = $register_user->contact->link;
		Log::info($link);
		$link = explode('-', $link);
		//例：1-8-11-13
		//从8开始往上找，包括8
		for ($i = count($link) - 3; $i >= 0; $i--) {
			$contact = LeftRight::where('uid', $link[$i])->first();
			if (!$contact) {
				continue;
			}
            Log::info($link[$i]);
			if ($i == 0) {
				$condition = $link[$i] . '-';
			} else {
				$condition = '-' . $link[$i] . '-';
			}
			//选取1.不同区 2.同层 的会员寻找碰对机会
			$touch_users = LeftRight::where('link', 'NOT LIKE', '%-' . $link[$i+1] . '-%')
				->where('link', 'LIKE', '%' . $condition . '%')
				->where('floor', $register_user->contact->floor)
				->get();
			$touch_user = [];
			//找到能发生碰对关系的会员
			foreach ($touch_users as $user) {
                $touchsl = Touchs::where('uid', $link[$i])
                    ->where('left_id', $user->uid)
                    ->first();
                $touchsr = Touchs::where('uid', $link[$i])
                    ->where('right_id', $user->uid)
                    ->first();
                if (!$touchsl && !$touchsr) {
                    $touch_user = Users::find($user->uid);
                    break;
                } else {
                    continue;
                }
			}
			//不存在碰对会员的话就继续往上找
			if (!$touch_user) {
				continue;
			}
            Log::info("存在碰对会员".$touch_user->account."\r\n");
			$contact_user = Users::find($link[$i]);
			// 计算当天获得的奖励
			$todayIncome = $this->todayIncome($contact_user);
            Log::info("计算当天获得的奖励".$contact_user->account.$todayIncome."\r\n");
            //1、受益如果是经销商或代理，封顶1000
            //2、按小碰
            if($contact_user->level == Users::DEALER_USER ||
                $contact_user->level == Users::PROXY_USER){
                $limit = Config::where('key', 'proxy_limit')->first(['value']);
                $limit = $limit->value;
            }
            //如果是代理商或服务中心
            if($contact_user->level == Users::CENTER_USER ||
                $contact_user->level == Users::COMMON_USER){
                $limit = Config::where('key', 'agent_limit')->first(['value']);
                $limit = $limit->value;
            }
            $floor_award = config('award.'.$this->config[$touch_user->level].'.floor_award');

            if($register_user->level == Users::DEALER_USER){
                $floor_award = config('award.'.$this->config[$register_user->level].'.floor_award');
            }

            if($touch_user->level == Users::DEALER_USER ||
                $touch_user->level == Users::PROXY_USER){
                $floor_award = config('award.'.$this->config[$touch_user->level].'.floor_award');
            }
            Log::info("层奖".$floor_award."\r\n");
			$this_points = $floor_award * 0.85; //扣除10%重消，5%税
			if ($todayIncome >= $limit) {
				continue;
			}
			if ($todayIncome < $limit) {
				if ($todayIncome + $this_points > $limit) {
					$this_points = $limit - $todayIncome;
					$floor_award = $this_points / 0.85;
				}
			}
			//获取受益人基本信息
			$contact_user = Users::where('id', $link[$i])->with('assets')->first();
            Log::info("获取受益人基本信息".$contact_user->account."\r\n");
			$original_amount = $contact_user->assets->added_points;
			//获取相对于获益人来说处于第几层
			$floor = $register_user->contact->floor - $contact->floor;
			//如果没有超出封顶值开始计算奖励
			//计算是否获得过层奖
			//若没有获得过，则计算为层奖
			$award = Dynamic::where('uid', $link[$i])
				->where('type', Dynamic::FROM_FLOOR_AWARD)
				->where('floor', $floor)
				->first();
			if (!$award) {
                Log::info($contact_user->account."计算为层奖\r\n");
				Dynamic::create([
					'uid' => $contact->uid,
					'deserve' => $floor_award,
					'tax' => $floor_award * 0.05,
					'repeat_consum' => $floor_award * 0.1,
					'realize' => $this_points,
					'type' => Dynamic::FROM_FLOOR_AWARD,
					'floor' => $floor,
					'mark' => '第' . $floor . '层层奖',
				]);
				//增加增值积分
				$contact_user->assets->added_points =
				$contact_user->assets->added_points + $this_points;
                $contact_user->assets->repeat_consum =
                    $contact_user->assets->repeat_consum + $floor_award * 0.1;
				$contact_user->assets->save();
				event(new \App\Events\AutoDeduction($contact_user));
				//添加碰对信息
				$touch = new Touchs;
				$touch->uid = $link[$i];
				$touch->left_id = $register_user->id;
				$touch->right_id = $touch_user->id;
				$touch->touch_type = Touchs::FROM_FLOOR;
				$touch->original_amount = $original_amount;
				$touch->current_amount = $contact_user->assets->added_points;
				$touch->flowing = '+' . $this_points;
				$touch->save();
				continue;
			}
			//若获得过，则计算量奖
			//获取直推人数
			$recommend_count = Users::where('recommend_code', $contact_user->account)
				->count();
            Log::info("$link[$i]"."获取直推人数$recommend_count\r\n");
			//根据直推人数计算获得的量奖
			if ($recommend_count >= config('award.'.$this->config[$contact_user->level].'.recommend4')) {
				$recommend_award = config('award.'.$this->config[$contact_user->level].'.recommend4_award');
			} else {
				$recommend_award = config('award.'.$this->config[$contact_user->level].'.recommend' . $recommend_count . '_award');
			}
			$this_points = $recommend_award * 0.85;
            Log::info('$this_points:'."$this_points\r\n");
			if ($todayIncome < $limit) {
				if ($todayIncome + $this_points > $limit) {
					$this_points = $limit - $todayIncome;
					$recommend_award = $this_points / 0.85;
				}
			}
			//计算受益人最高可获得几次量奖
			$available_count = pow(2, $floor) / 2 - 1;
            Log::info("计算受益人最高可获得几次量奖:$available_count\r\n");
			//获得本楼层量奖次数，判断是否给量奖
			$volume_count = Dynamic::where('uid', $link[$i])
				->where('type', Dynamic::FROM_VOLUME_AWARD)
				->where('floor', $floor)
				->count();
			$original_amount = $contact_user->assets->added_points;
			if ($volume_count < $available_count) {
				Dynamic::create([
					'uid' => $contact_user->id,
					'deserve' => $recommend_award,
					'tax' => $recommend_award * 0.05,
					'repeat_consum' => $recommend_award * 0.1,
					'realize' => $this_points,
					'type' => Dynamic::FROM_VOLUME_AWARD,
					'floor' => $floor,
					'mark' => '第' . $floor . '层量奖',
				]);
				//增加增值积分
				$contact_user->assets->added_points =
				$contact_user->assets->added_points + $this_points;
                $contact_user->assets->repeat_consum =
                    $contact_user->assets->repeat_consum + $recommend_award * 0.1;
				$contact_user->assets->save();
				//添加碰对信息表***************
				$touch = new Touchs;
				$touch->uid = $link[$i];
				$touch->left_id = $register_user->id;
				$touch->right_id = $touch_user->id;
				$touch->touch_type = Touchs::FROM_VOLUME;
				$touch->original_amount = $original_amount;
				$touch->current_amount = $contact_user->assets->added_points;
				$touch->flowing = '+' . $this_points;
				$touch->save();
				//感恩奖，发五代
				$this->handleOwed($contact_user, $this_points);
			}
		}
	}
	public function handleOwed($contact_user, $points,&$num = 0) {
		//感恩奖发五代,直推
		//拿下五代量碰收入的 10%，5%，3%，2%，1%。
		$array = [10, 5, 3, 2, 1];
        $owed = $points * $array[$num] * 0.01;
        $user = Users::where('id', $contact_user->contact->recommend_id)
            ->with('assets')
            ->with('contact')
            ->first();
        if(!$user){
            return;
        }
        // 计算当天获得的奖励
        $todayIncome = $this->todayIncome($user);
        Log::info("计算当天获得的奖励".$user->account.$todayIncome."\r\n");
        //1、受益如果是经销商或代理，封顶1000
        //2、按小碰
        if($user->level == Users::DEALER_USER ||
            $user->level == Users::PROXY_USER){
            $limit = Config::where('key', 'proxy_limit')->first(['value']);
            $limit = $limit->value;
        }
        //如果是代理商或服务中心
        if($user->level == Users::CENTER_USER ||
            $user->level == Users::COMMON_USER){
            $limit = Config::where('key', 'agent_limit')->first(['value']);
            $limit = $limit->value;
        }
        $this_points = $owed * 0.85; //扣除10%重消，5%税
        if ($todayIncome < $limit) {
            if ($todayIncome + $this_points > $limit) {
                $this_points = $limit - $todayIncome;
                $owed = $this_points / 0.85;
            }
            //获取相对于获益人来说处于第几层
            $floor = $contact_user->contact->floor - $user->contact->floor;
            Dynamic::create([
                'uid' => $user->id,
                'deserve' => $owed,
                'tax' => $owed * 0.05,
                'repeat_consum' => $owed * 0.1,
                'realize' => $this_points,
                'type' => Dynamic::FROM_OWED_AWARD,
                'floor' => $floor,
                'mark' => '第' . $floor . '代感恩奖',
            ]);
            //增加增值积分
            $user->assets->added_points =
                $user->assets->added_points + $owed * 0.85;
            $user->assets->repeat_consum =
                $user->assets->repeat_consum + $owed * 0.1;
            $user->assets->save();
        }
        $num++;
        $this->handleOwed($user,$points,$num);
	}
}
