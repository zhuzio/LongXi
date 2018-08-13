<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankCard extends Model
{
    use SoftDeletes;

    protected $table = 'bank_card';
    protected $guarded = ['id'];
    protected $datas = ['deleted_at'];
}