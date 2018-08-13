<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transform extends Model {

    protected $guarded = ['id'];

    //广告积分转增值积分
    const TRANSFORM_TO_ADDEDPOINTS = 1;
    //增值积分转电子币
    const TRANSFORM_TO_ELECTRONIC = 2; //层碰
    public function user() {
        return $this->belongsTo(Users::class, 'uid', 'id');
    }
}
