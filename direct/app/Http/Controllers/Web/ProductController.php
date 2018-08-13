<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller {
	public function index(Request $request) {
		$list = Product::where('uid', $request->user->id)
			->get();
		return [
			'code' => 200,
			'data' => $list,
		];
	}
}
