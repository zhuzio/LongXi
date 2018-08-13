<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model {
	public function user() {
		return $this->belongsTo(Users::class, 'uid', 'id');
	}
}
