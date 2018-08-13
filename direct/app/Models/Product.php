<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	protected $guarded = ['id'];

	public function setPicsAttribute($pictures) {
		if (is_array($pictures)) {
			$this->attributes['pics'] = implode(',', $pictures);
		}
	}

	public function getPicsAttribute($pictures) {
		return empty($pictures) ? [] : explode(',', $pictures);
	}

	public function setStartDateAttribute($value) {
		$this->attributes['start_date'] = $value ? strtotime($value) : 0;
	}

	public function setEndDateAttribute($value) {
		$this->attributes['end_date'] = $value ? strtotime($value) : 0;
	}
}