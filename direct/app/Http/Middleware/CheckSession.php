<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Vinkla\Hashids\Facades\Hashids;

class CheckSession {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (!$request->session()->get('id')) {
			return redirect('/login');
		}
		$uid = Hashids::decode($request->session()->get('id'));
		$user = Users::where('id', $uid[0])
			->with('assets')
			->with('contact')
			->first();
		if (!$user) {
			return redirect('/login');
		}
		//被禁用做判断
		if ($user->stoped == 1) {
			return redirect('/login');
		}
		$request->user = $user;

		return $next($request);
	}
}
