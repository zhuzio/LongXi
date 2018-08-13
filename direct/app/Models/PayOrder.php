<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayOrder extends Model
{
    const SHOP_POINTS_PAY = 1;//购物积分支付

    const FROM_ONLINE = 1; //线上订单
    const FROM_TRANFER = 2; //转账

    public function createSN()
    {
        return 'PA' . date('YmdHis') . $this->id . mt_rand(1000, 9999);
    }
}
