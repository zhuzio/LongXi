<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfers extends Model {

    const TRANSFER_TO_ELECTRONIC = 1; //电子币转账
    const TRANSFER_TO_SHOPPOINTS = 2; //电子币转购物积分
    const TRANSFER_SHOPPOINTS = 3; //电子币转购物积分

    protected $table = 'transfers';
    protected $guarded = ['id'];

	public function fromuser() {
		return $this->belongsTo(Users::class, 'from', 'id');
	}
    public function touser() {
        return $this->belongsTo(Users::class, 'to', 'id');
    }
}
