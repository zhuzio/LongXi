<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Assets;
use App\Models\BankCard;
use App\Models\BankList;
use App\Models\LeftRight;
use App\Models\SmsLog;
use App\Models\Users;
use App\Service\Sms;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Validator;
use Vinkla\Hashids\Facades\Hashids;
use DB;
class UserController extends Controller {
	public function login(Request $request) {
		if ($request->isMethod('get')) {
			return view('web.user.login');
		}
		$account = $request->account;
		$password = $request->password;
		$user = Users::where('account', $account)
			->first();
		if (!$user) {
			return ['code' => 500, 'msg' => '用户不存在'];
		}
		if (!Hash::check($password, $user->password)) {
			return ['code' => 500, 'msg' => '密码不正确'];
		}
		$id = Hashids::encode($user->id);
		$request->session()->put('id', $id);
		return ['code' => 200, 'msg' => '登录成功'];
	}
	public function loginOut(Request $request) {
		$request->session()->put('id', '');
		return redirect('/login');
	}
	private function getUser($code) {
		return Users::where('account', $code)
			->where('stoped', Users::DEFAULT_USER_STATUS)
			->where('is_check', Users::CHECK_AGREE)
			->with('contact')
			->first();
	}
	public function register(Request $request) {
		if (!$request->isMethod('post')) {
			$bank = BankList::all();
			$data['account'] = randomkeys();
			$data['bank'] = $bank;
			return view('web.user.register', ['data' => $data]);
		}

		$validator = Validator::make(
			$request->all(),
			[
				'account' => 'required',
				'recommend_code' => 'required',
				'contact_code' => 'required',
				'center_code' => 'required',
				'place' => 'required',
                'level' => 'required',
				'realname' => 'required',
				'sex' => 'required',
				'id_number' => 'required',
				'phone' => 'required|regex:/^1[3456789][0-9]{9}$/',
				'province' => 'required',
				'city' => 'required',
				'country' => 'required',
				'detail' => 'required',
				"cpt" => 'required|captcha',
			],
			[
				'account.required' => '缺少账号id',
				'recommend_code.required' => '缺少老顾客',
				'contact_code.required' => '缺少新顾客',
				'center_code.required' => '缺少报单中心',
				'place.required' => '请选择顾客区',
                'level.required' => '请选择客户类型',
				'realname.required' => '姓名必须',
				'sex.required' => '性别必须',
				'id_number.required' => '身份证必须',
				'phone.required' => '缺少手机号',
                'phone.regex' => '手机号格式错误',
				'province.required' => '省份必须',
				'city.required' => '城市必须',
				'country.required' => '县\区必须',
				'detail.required' => '详细地址必须',
				'cpt.required' => '请输入验证码',
				'cpt.captcha' => '验证码错误，请重试',
			]
		);

		if ($validator->fails()) {
			$errors = $validator->errors()->all();

			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}
		$recommend_code = $request->recommend_code; //老顾客
		$contact_code = $request->contact_code; //新顾客
		$center_code = $request->center_code; //报单中心
		$recommend_user = self::getUser($recommend_code);
		if (!$recommend_user) {
			return [
				'code' => 500,
				'msg' => '老顾客不存在',
			];
		}
		$contact_user = self::getUser($contact_code);
		if (!$contact_user) {
			return [
				'code' => 500,
				'msg' => '新顾客不存在',
			];
		}
		$place = $request->place;
		if ($contact_user->contact->left_id &&
			$place == 1
		) {
			return [
				'code' => 500,
				'msg' => '新顾客左区已经被占了',
			];
		}
		if ($contact_user->contact->right_id &&
			$place == 2
		) {
			return [
				'code' => 500,
				'msg' => '新顾客右区已经被占了',
			];
		}
		$place_user = Users::where('contact_code',$contact_code)
            ->where('place',$place)
            ->first();
		if($place_user){
            return [
                'code' => 500,
                'msg' => '该位置已经被占了',
            ];
        }
		//如果选择注册在新顾客右区，但是新顾客没有直推
		$has_recommend = LeftRight::where('pid', $contact_user->id)->first();
		if ($place == 2 &&
			!$has_recommend) {
			return [
				'code' => 500,
				'msg' => '左区没有直推不能安置右区',
			];
		}
		$center_user = self::getUser($center_code);
		if (!$center_user) {
			return [
				'code' => 500,
				'msg' => '服务中心不存在',
			];
		}
        if ($center_user->level != Users::CENTER_USER) {
            return [
                'code' => 500,
                'msg' => '该用户不是服务中心',
            ];
        }
        $id_num_count = DB::table('users')
            ->where('id_number',$request->id_number)
            ->count();
		if($id_num_count >= 7){
            return [
                'code' => 500,
                'msg' => '该证件号注册上限已满',
            ];
        }
        $password = Hash::make('111111');
		$payment_password = Hash::make('222222');
		$user = Users::create([
			'account' => $request->account,
			'phone' => $request->phone,
			'password' => $password,
			'payment_password' => $payment_password,
			'id_number' => $request->id_number,
			'recommend_code' => $recommend_code,
			'contact_code' => $contact_code,
			'center_code' => $center_code,
			'realname' => $request->realname,
			'place' => $place,
			'sex' => $request->sex,
            'level' => $request->level,
		]);
		Address::create([
			'uid' => $user->id,
			'name' => $request->realname,
			'phone' => $request->phone,
			'province' => $request->province,
			'city' => $request->city,
			'country' => $request->country,
			'detail' => $request->detail,
			'is_default' => 1,
		]);
		if ($request->bank_name &&
			$request->bank_account &&
			$request->bank_card &&
			$request->bank_code &&
			$request->bank_code_name) {
			BankCard::create([
				'uid' => $user->id,
				'bank' => $request->bank_name,
				'account' => $request->bank_account,
				'id_number' => $request->id_number,
				'card' => $request->bank_card,
				'phone' => $request->phone,
				'bank_code' => $request->bank_code,
				'bank_code_name' => $request->bank_code_name,
				'is_default' => 1,
			]);
		}
		if (!$user) {
			return [
				'code' => 500,
				'msg' => '注册失败',
			];
		}
		//添加个人资产表
		$assets = new Assets;
		$assets->uid = $user->id;
		$assets->save();
		return [
			'code' => 200,
			'msg' => '注册成功,等待服务中心审核',
		];
	}
    public function sendSms(Request $request)
    {
        $phone = $request->phone;
        $code = rand(1000, 9999);
        $sms_log = new SmsLog;
        $sms_log->ip = $request->ip();
        $sms_log->phone = $phone;
        $sms_log->code = $code;
        $sms_log->save();
        $result = Sms::send($code, $phone);
        $sms_log->result = $result['msg'];
        $sms_log->save();

        if ($result['code'] == 0) {
            return  [
                'code' => 200,
                'msg' => '发送成功',
            ];
        }

        return  [
            'code' => 500,
            'msg' => '发送失败, 请重试',
        ];
    }

    public function checkSmsCode(Request $request)
    {
        $sms_log = SmsLog::where('phone', $request->phone)
            ->where('code', $request->code)
            ->first();
        if (!$sms_log) {
            return  [
                'code' => 500,
                'msg' => '验证码错误',
            ];
        }
        if ($sms_log->status == 1) {
            return  [
                'code' => 500,
                'msg' => '验证码已失效, 请重新获取',
            ];
        }
        if (Carbon::now()->modify('-5 minutes')->gt($sms_log->created_at)) {
            return  [
                'code' => 500,
                'msg' => '验证码已过期, 请重新获取',
            ];
        }
        $sms_log->used = 1;
        $sms_log->save();
        return  [
            'code' => 200,
            'msg' => '验证成功',
        ];
    }
	public function checkUser(Request $request) {
		$user = Users::where('account', $request->code)
			->first();
		if (!$user) {
			return ['code' => 500, 'msg' => '用户不存在'];
		}
		if ($user->stoped == 1) {
			return ['code' => 500, 'msg' => '该用户已被冻结'];
		}
		if ($user->is_check == 0) {
			return ['code' => 500, 'msg' => '该用户还未被审核'];
		}
		if ($user->level == '代理商' &&
			$request->type == 'service') {
			return ['code' => 500, 'msg' => '该用户不是服务中心'];
		}
		return ['code' => 200, 'msg' => '用户存在，该用户是 ' . $user->realname];
	}
	public function myRecommend(Request $request) {
		$list = Users::where('recommend_code', $request->user->account)
            ->orderBy('id','desc')
			->paginate(15);
		return view('web.user.myRecommend', ['data' => $list]);
	}
    public function centerRecommend(Request $request) {
        $list = Users::where('recommend_code', $request->user->account)
            ->orderBy('id','desc')
            ->paginate(15);
        return view('web.user.centerRecommend', ['data' => $list]);
    }
	public function resetPassword(Request $request) {
		if ($request->isMethod('get')) {
			return view('web.user.resetPassword');
		}
		$validator = Validator::make(
			$request->all(),
			[
				'old_password' => 'required',
				'password' => [
					'required',
					'confirmed',
				],
				'password_confirmation' => 'required',
			],
			[
				'old_password.required' => '请输入原始密码',
				'password.required' => '请输入新密码',
				'password_confirmation.required' => '请输入确认密码',
				'password.confirmed' => '新密码和确认密码不同',
			]
		);

		if ($validator->fails()) {
			$errors = $validator->errors()->all();

			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}

		$user = $request->user;

		if (!Hash::check($request->old_password, $user->password)) {
			return [
				'code' => 500,
				'msg' => '原始密码错误',
			];
		}
		$user->password = Hash::make($request->password);
		$user->save();
		return [
			'code' => 200,
			'msg' => '修改成功',
		];
	}
	public function resetPayment(Request $request) {
		if ($request->isMethod('get')) {
			return view('web.user.resetPayment');
		}
		$validator = Validator::make(
			$request->all(),
			[
				'old_password' => 'required',
				'password' => [
					'required',
					'confirmed',
				],
				'password_confirmation' => 'required',
			],
			[
				'old_password.required' => '请输入原始密码',
				'password.required' => '请输入新密码',
				'password_confirmation.required' => '请输入确认密码',
				'password.confirmed' => '新密码和确认密码不同',
			]
		);

		if ($validator->fails()) {
			$errors = $validator->errors()->all();

			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}

		$user = $request->user;

		if (!Hash::check($request->old_password, $user->payment_password)) {
			return [
				'code' => 500,
				'msg' => '原始密码错误',
			];
		}

		$user->payment_password = Hash::make($request->password);
		$user->save();

		return [
			'code' => 200,
			'msg' => '修改成功',
		];
	}
	public function feedback(){
            return view('web.user.feedback');
    }
}
