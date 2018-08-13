<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AutoReturn;
use App\Models\Dynamic;
use App\Models\LeftRight;
use App\Models\Statics;
use App\Models\Users;
use Carbon\Carbon;
use DB;
use Log;

class AutoController extends Controller {
	/**
	 * 广告积分每日定返
	 * 回本以后就不返了
	 * @return [type] [description]
	 */
	public function advertPointsReturn() {
		$daily = DB::table('daily_return')
			->orderBy('id', 'desc')
			->first();
		$t1 = $daily->return_at;
		$dt = Carbon::now()->diffInDays($t1);
		if ($dt < 1) {
			Log::info('不满一天不定返');
			return;
		}
		Users::where('stoped', 0)
			->where('is_check', 1)
			->chunk(100, function ($users) {
				foreach ($users as $user) {
					$return = AutoReturn::where('uid', $user->id)
						->first();
					if(!$return){
					    continue;
                    }
					if ($return->remaining_days <= 0) {
						Log::info('剩余天数等于0,continue');
						continue;
					}
					//查询是否回本
					$back = Dynamic::where('uid', $user->id)
						->where('type', Dynamic::FROM_BACK_AWARD)
						->first();
					//查询是否推荐
					$recommend = LeftRight::where('recommend_id', $user->id)
						->first();
					//如果回本了，不做处理，继续
					if ($back) {
						Log::info('已经回本,continue');
						continue;
					}
					//如果有推荐，并且没有回本
					$advert_award = $return->daily_return; //广告积分
					if ($return->remaining_days == 1) {
						$advert_award = $return->last_return;
					}
                    Log::info('广告积分定返,金额' . $advert_award);
                    Statics::create([
                        'uid' => $user->id,
                        'deserve' => $advert_award,
                        'tax' => $advert_award * 0.05,
                        'repeat_consum' => $advert_award * 0.1,
                        'realize' => $advert_award * 0.85,
                        'mark' => '广告积分定返',
                    ]);
                    DB::table('assets')
                        ->where('uid', $user->id)
                        ->increment('advert_points', $advert_award * 0.85);
                    DB::table('assets')
                        ->where('uid', $user->id)
                        ->increment('repeat_consum', $advert_award * 0.1);
					//更新每日定返信息表
					$return->already_return_days =
					$return->already_return_days + 1;
					$return->remaining_days =
					$return->remaining_days - 1;
					$return->already_return =
					$return->already_return + $advert_award;
					$return->save();

				}
			});
        //更新每日定返时间记录表
        DB::table('daily_return')->insert(['return_at' => Carbon::today()->modify('+1 hours')]);

	}
}
