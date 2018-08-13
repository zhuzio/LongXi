<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Touchs extends Model {
    const FROM_BACK = 1; //回本
    const FROM_FLOOR = 2; //层碰
    const FROM_VOLUME = 3; //量奖
    public function user() {
        return $this->belongsTo(Users::class, 'uid', 'id');
    }
}
