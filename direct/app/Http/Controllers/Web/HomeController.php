<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeftRight;
use Illuminate\Http\Request;
use Log;
use App\Models\Users;
class HomeController extends Controller {
	public function index(Request $request) {
        return view('web.home.index',['data'=>$request->user]);
	}
    public function welcome() {
        return view('web.home.welcome');
    }
	public function testCapital() {
		return view('web.home.testCapital');
	}
}
