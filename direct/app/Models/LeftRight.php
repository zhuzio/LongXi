<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
class LeftRight extends Model {
	protected $table = 'left_right';
    protected $guarded = ['id'];
	//用户信息
	public function user() {
		return $this->belongsTo(Users::class, 'uid');
	}
	//推荐人
	public function recommend() {
		return $this->belongsTo(Users::class, 'recommend_id', 'id');
	}
	//安置人
	public function placement() {
		return $this->belongsTo(Users::class, 'pid', 'id');
	}
}
