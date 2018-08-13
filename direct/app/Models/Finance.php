<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model {

    const WITHDRAW = 1; //购买电子币
    const BUY = 2; //电子币提现

	protected $table = 'finance';
	public function user() {
		return $this->belongsTo(Users::class, 'to', 'account');
	}

}
