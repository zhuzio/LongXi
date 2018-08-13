<?php

namespace App\Listeners;

use App\Events\AutoDeduction;
use App\Models\Assets;
use App\Models\AutoDeduction as AutoDeductionModel;
use App\Models\AutoReturn;
use App\Models\Dynamic;
use App\Models\LeftRight;
use App\Models\Users;
use Carbon\Carbon;
use DB;
use Log;
use App\Models\Config;
class TenGenerationRewards {
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
	}
	/**
	 * Handle the event.
	 *
	 * @param  AutoDeduction  $event
	 * @return void
	 */
	public function handle(AutoDeduction $event) {
		$info = "十代奖励\r\n";
		$user = $event->user;
		$month_start = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
		$month_end = date('Y-m-d 11:59:59', strtotime("$month_start +1 month -1 day"));
		//查询本月是否已经扣过339了
		//扣过就不再扣了
		$deduction = AutoDeductionModel::where('uid', $user->id)
			->whereBetween('created_at', [$month_start, $month_end])
			->first();
		$info .= "查询是否扣过339\r\n";
		if ($deduction) {
			return;
		}
		if($user->level == Users::CENTER_USER ||
            $user->level == Users::COMMON_USER){
            $info .= "查询是否回本\r\n";
            //查询是否回本
            $auto_return = AutoReturn::where('uid', $user->id)
                ->first();
            //没有回本
            if ($auto_return->remaining_days != 0) {
                return;
            }
            //查询回本以后收入是否超过500元，
            $money = DB::table('dynamics')
                ->where('uid', $user->id)
                ->where('type', '<>',Dynamic::FROM_SERVICE_AWARD)
                ->where('type', '<>',Dynamic::FROM_BACK_AWARD)
                ->sum('realize');
            if($money < 500){
                return;
            }
        }

		$info .= "查询本月收益是否满500\r\n";
		//查询本月收益是否满500
		$month_award = $this->getMonthAward($user);
		if ($month_award < 500) {
			return;
		}
		$info .= $user->id . "开始扣除339\r\n";
		//开始扣除339
		$assets = Assets::where('uid', $user->id)
			->first();
		$first_added_points = $assets->added_points;
		$first_shop_points = $assets->shop_points;
		$assets->added_points = $assets->added_points - 339;
		$assets->shop_points = $assets->shop_points + 339;
		$assets->save();
		$last_added_points = $assets->added_points;
		$last_shop_points = $assets->shop_points;
		Log::info($info);
		AutoDeductionModel::create([
			'uid' => $user->id,
			'first_points' => $first_added_points,
			'points' => 339,
			'last_points' => $last_added_points,
		]);
		DB::table('shop_points_log')->insert([
			'uid' => $user->id,
			'first_points' => $first_shop_points,
			'points' => 339,
			'mark' => '系统转入',
			'last_points' => $last_shop_points,
			'created_at' => Carbon::now(),
		]);
		//计算十代奖励
		$this->tenGenerationRewards($user);
	}
	/**
	 * 发十代奖
	 * 紧缩发放（
	 * 当第一代没有满足扣除条件，第二代满足了扣除条件，
	 * 第二代自动变成第一代，以此类推
	 * ）
	 * @param $user
	 */
	public function tenGenerationRewards($user) {
		Log::info("十代收益\r\n");
		$lr = LeftRight::where('uid', $user->id)->first();
		Log::info('link' . $lr->link . "\r\n");
		$link = explode('-', $lr->link);
		//link:1-2-3-4-5-6-7-8-9-10-11，以这个举例计算

		for ($i = count($link) - 2; $i >= count($link) - 11; $i--) {
			if ($i < 0) {
				break;
			}
			//例子：link数组从10开始取，
			$son_generation = array_slice($link, $i);
			Log::info('son_generation array' . json_encode($son_generation) . "\r\n");
			//查询线下符合条件的数量
			$son = 0;
			for ($k = 1; $k < count($son_generation); $k++) {
				$son_user = Users::find($son_generation[$k]);
				//查询月收益是否满500

				$month_award = $this->getMonthAward($son_user);
				Log::info($son_user->account . $month_award . "\r\n");
				if ($month_award < 500) {
					continue;
				}
                if($son_user->level == Users::CENTER_USER ||
                    $son_user->level == Users::COMMON_USER){
                    Log::info($son_user->account ."查询是否回本\r\n");
                    //查询是否回本
                    $return = AutoReturn::where('uid', $user->id)
                        ->first();
                    //没有回本
                    if ($return->remaining_days > 0) {
                        continue;
                    }
                }
				$son++;
			}
			Log::info('$son' . $son . "\r\n");
			if ($son == 0) {
				continue;
			}
			Log::info('$link[$i]' . $link[$i] . "\r\n");
			//给$i发放十代奖，$son几个就发几代
			for ($n = 0; $n < $son; $n++) {
				$link_user = Users::find($link[$i]);
				// 计算当天获得的奖励
				$todayIncome = $this->todayIncome($link_user);
                $ten_reward = [6, 5, 4, 3, 2, 2, 3, 4, 5, 6];
                Log::info('$n' . $n . "\r\n");
                Log::info('$ten_reward[$n]' . $ten_reward[$n] . "\r\n");
                $ten_reward = $ten_reward[$n] * 0.01 * 339;
				$this_points = $ten_reward * 0.85; //扣除10%重消，5%税
                if($link_user->level == Users::DEALER_USER ||
                    $link_user->level == Users::PROXY_USER){
                    $limit = Config::where('key', 'proxy_limit')->first(['value']);
                    $limit = $limit->value;
                }
                //如果是代理商或服务中心
                if($link_user->level == Users::CENTER_USER ||
                    $link_user->level == Users::COMMON_USER){
                    $limit = Config::where('key', 'agent_limit')->first(['value']);
                    $limit = $limit->value;
                }
				if ($todayIncome >= $limit) {
                    Log::info('$link[$i]'.$link[$i]."当日收益已经>=$limit\r\n");
					continue;
				}
				if ($todayIncome < $limit) {
					if ($todayIncome + $this_points > $limit) {
						$this_points = $limit - $todayIncome;
						$ten_reward = $this_points / 0.85;
					}
				}
				Dynamic::create([
					'uid' => $link[$i],
					'deserve' => $ten_reward,
					'tax' => $ten_reward * 0.05,
					'repeat_consum' => $ten_reward * 0.1,
					'realize' => $this_points,
					'type' => Dynamic::FROM_TEN_AWARD,
					'floor' => 0,
					'mark' => '十代奖',
				]);
				DB::table('assets')
					->where('uid', $link[$i])
					->increment('added_points', $this_points);
                DB::table('assets')
                    ->where('uid', $link[$i])
                    ->increment('repeat_consum', $ten_reward * 0.1);
				event(new \App\Events\AutoDeduction($link_user));
			}
		}
	}
	public function getMonthAward($user) {
		$month_start = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
		$month_end = date('Y-m-d 11:59:59', strtotime("$month_start +1 month -1 day"));
		//查询本月收益
		return DB::table('dynamics')
			->where('uid', $user->id)
			->whereBetween('created_at', [$month_start, $month_end])
			->sum('realize');
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
}
