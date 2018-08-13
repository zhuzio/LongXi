<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectronicLog extends Model {


    protected $table = 'electronic_log';
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(Users::class, 'uid', 'id');
    }

}