<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ApplyWithdraw extends Model
{
    protected $table = 'apply_withdraw';
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(Users::class, 'uid', 'id');
    }
    public function center() {
        return $this->belongsTo(Users::class, 'center_id', 'id');
    }
    public function bankCard()
    {
        return $this->belongsTo(BankCard::class, 'bank_id', 'id');
    }
}