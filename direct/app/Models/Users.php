<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Users extends Model {

	const CHECK_AGREE = 1; //审核通过
	const CHECK_REFUSE = 2; //审核拒绝

    //为了后台做用户信息excel导出，这里输出为名称
	const COMMON_USER = '代理商'; //普通用户\代理商（7882）1
	const CENTER_USER = '服务中心'; //服务中心 2
    const DEALER_USER = '经销商'; // 3
    const PROXY_USER = '代理'; //服务中心 4

    const FEMALE_USER = 0; //女性用户
    const MALE_USER= 1; //男性用户

    const DEFAULT_USER_STATUS = 0; //正常用户
    const DISABLED_USER_STATUS = 1; //被禁用用户

	protected $table = 'users';

	protected $guarded = ['id'];
	public function assets() {
		return $this->hasOne(Assets::class, 'uid', 'id');
	}
	public function contact() {
		return $this->hasOne(LeftRight::class, 'uid', 'id');
	}

	 public function setLevelAttribute($level) {
         if ($level == '代理商' || $level == 1) {
             $this->attributes['level'] = 1;
         }
	 	if ($level == '服务中心' || $level == 2) {
	 		$this->attributes['level'] = 2;
	 	}
         if ($level == '经销商' || $level == 3) {
             $this->attributes['level'] = 3;
         }
         if ($level == '代理' || $level == 4) {
             $this->attributes['level'] = 4;
         }
	 }
	public function getLevelAttribute($level) {
        if ($level == 1) {
            $level = '代理商';
        }
        if ($level == 2) {
            $level = '服务中心';
        }
        if ($level == 3) {
            $level = '经销商';
        }
        if ($level == 4) {
            $level = '代理';
        }
        return $level;

	}

}
