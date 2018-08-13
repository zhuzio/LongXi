<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCenter extends Model {
	protected $table = 'report_center_apply';
	public function user() {
		return $this->belongsTo(Users::class, 'uid', 'id');
	}

}
