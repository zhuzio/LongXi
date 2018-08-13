<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	const WAIT_PAY = 0; //支付状态 未支付
	const PAYED = 1; //已支付

    protected $guarded = ['id'];

	public function user() {
		return $this->belongsTo(Users::class, 'uid', 'id');
	}
	public function product() {
		return $this->belongsTo(Product::class, 'product_id', 'id');
	}
    public function address() {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
}
