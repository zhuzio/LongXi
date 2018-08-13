<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transform;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

//转换（广告积分转增值积分，增值积分转电子币）
class TransformController extends Controller {

	//广告积分转增值积分
	public function transformAddedPoints(Request $request) {

		if ($request->isMethod('get')) {
			$data['total'] = $request->user->assets->advert_points;
			$data['available'] = floor($request->user->assets->advert_points / 100) * 100;
			return view('web.transform.transformAddedPoints', ['data' => $data]);
		}
		$user = $request->user;
		$points = $request->points;
		if ($points % 100 !== 0 ||
			$points < 100) {
			return ['code' => 500, 'msg' => '广告积分以整百转出'];
		}
		if ($points > $user->assets->advert_points) {
			return ['code' => 500, 'msg' => '广告积分不足'];
		}
		$rules = [
			"cpt" => 'required|captcha',
		];
		$messages = [
			'cpt.required' => '请输入验证码',
			'cpt.captcha' => '验证码错误，请重试',
		];
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}
		Transform::create([
			'uid' => $user->id,
			'points' => $points,
			'type' => Transform::TRANSFORM_TO_ADDEDPOINTS,
		]);
		$user->assets->added_points =
		$user->assets->added_points + $points;
		$user->assets->advert_points =
		$user->assets->advert_points - $points;
		$user->assets->save();
		return ['code' => 200, 'msg' => '转换成功，请刷新页面查看'];
	}
	public function transformAddedPointsList(Request $request) {
		$user = $request->user;
		$list = Transform::where('uid', $user->id)
			->where('type', Transform::TRANSFORM_TO_ADDEDPOINTS)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->paginate(15);
		return view('web.transform.transformAddedPointsList', ['data' => $list]);
	}
	//增值积分转电子币
	public function transformElectronic(Request $request) {
		if ($request->isMethod('get')) {
			$data['total'] = $request->user->assets->added_points;
			$data['available'] = floor($request->user->assets->added_points / 100) * 100;
			return view('web.transform.transformElectronic', ['data' => $data]);
		}
		$user = $request->user;
		$points = $request->points;
		if ($points % 100 !== 0) {
			return ['code' => 500, 'msg' => '增值积分以整百转出'];
		}
		if ($points > $user->assets->added_points) {
			return ['code' => 500, 'msg' => '增值积分不足'];
		}
		$rules = [
			"cpt" => 'required|captcha',
		];
		$messages = [
			'cpt.required' => '请输入验证码',
			'cpt.captcha' => '验证码错误，请重试',
		];
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}
		Transform::create([
			'uid' => $user->id,
			'points' => $points,
			'type' => Transform::TRANSFORM_TO_ELECTRONIC,
		]);
		$user->assets->electronic =
		$user->assets->electronic + $points;
		$user->assets->added_points =
		$user->assets->added_points - $points;
		$user->assets->save();
		return ['code' => 200, 'msg' => '转换成功，请刷新页面查看'];
	}
	public function transformElectronicList(Request $request) {
		$user = $request->user;
		$list = Transform::where('uid', $user->id)
			->where('type', Transform::TRANSFORM_TO_ELECTRONIC)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '<', Carbon::tomorrow())
			->paginate(15);
		return view('web.transform.transformElectronicList', ['data' => $list]);
	}
}
