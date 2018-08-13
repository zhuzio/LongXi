<?php
use Illuminate\Support\Facades\DB;
function randomkeys() {
	// $pattern = 'abcdefghijklmnopqrstuvwxyz';
	// $key = '';
	// for ($i = 0; $i < 3; $i++) {
	// 	$key .= $pattern{mt_rand(0, 25)};
	// }
	$key = '';
	$pattern1 = '1234567890';
	for ($i = 0; $i < 7; $i++) {
		$key .= $pattern1{mt_rand(0, 9)};
	}
	$user = DB::table('users')->where('account', $key)
		->first();
	if ($user) {
		randomkeys();
	}
	return $key;
}