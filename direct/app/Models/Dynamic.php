<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dynamic extends Model {

	const FROM_SERVICE_AWARD = 1; //服务奖
	const FROM_BACK_AWARD = 2; //一层回本
	const FROM_FLOOR_AWARD = 3; //层奖
	const FROM_VOLUME_AWARD = 4; //量奖
    const FROM_OWED_AWARD = 5; //感恩奖
    const FROM_TEN_AWARD = 6; //十代奖

    protected $table = 'dynamics';

    protected $guarded = ['id'];
	public function user() {
		return $this->belongsTo(Users::class, 'uid', 'id');
	}
    public function getTypeAttribute($type)
    {
        $types = [1=>'服务奖',2=>'回本',3=>'层奖',4=>'量奖',5=>'感恩奖',6=>'十代奖'];
        return $types[$type];

    }
}
