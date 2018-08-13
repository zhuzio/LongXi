<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BankCard;
use App\Models\BankList;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use DB;
class BankController extends Controller {

	public function banklist(Request $request) {
		$list = BankCard::where('uid', $request->user->id)
			->get(['id', 'account', 'bank', 'card', 'created_at']);

		foreach ($list as &$v) {
			$v['account'] = Str::substr($v['account'], 0, 1) . '**';
			$v['card'] = Str::substr($v['card'], 0, 2) . '****' . Str::substr($v['card'], -4);
		}
		unset($v);
		return view('web.bank.banklist', ['data' => $list]);
	}
	public function bankCardDelete(Request $request) {
		$deleted = BankCard::destroy($request->bank_id);

		return ['code' => 200, 'msg' => '删除成功'];
	}
	public function addBankCard(Request $request) {
		$validator = Validator::make(
			$request->all(),
			[
				'bank' => 'required',
				'account' => 'required',
				'id_number' => [
					'required',
					'regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/',
				],
				'card' => 'required|numeric',
				'phone' => "required|regex:/^1[34578][0-9]{9}$/",
			],
			[
				'bank.required' => '请选择银行名称',
				'account.required' => '请输入开户姓名',
				'id_number.required' => '请输入身份证号',
				'id_number.regex' => '身份证号不合法',
				'phone.required' => '请输入手机号',
				'phone.regex' => '请输入正确的手机号',
			]
		);
		if ($validator->fails()) {
			$errors = $validator->errors()->all();
			return [
				'code' => 500,
				'msg' => $errors[0],
			];
		}
		BankCard::create([
			'uid' => $request->user->id,
			'bank' => $request->bank,
			'account' => $request->account,
			'id_number' => $request->id_number,
			'card' => $request->card,
			'phone' => $request->phone,
		]);

		return [
			'code' => 200,
			'msg' => '添加成功',
		];
	}
    /**
     * 获取开户行列表
     * @return [type] [description]
     */
    public function banksList() {
        $bank = BankList::all();
        return ['code' => 200, 'data' => $bank];
    }
    /**
     * 获取开户行省份
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bankProvince(Request $request) {
        $bank = $request->bank;
        if (!$bank) {
            return ['code' => 500, 'msg' => '请选择开户行！'];
        }
        $province = DB::table('account_bank')
            ->where('bank', $bank)
            ->groupBy('province')
            ->get(['province']);
        return ['code' => 200, 'data' => $province];
    }
    /**
     * 获取开户行城市
     * bank 开户行
     * province 开户省份
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bankCity(Request $request) {
        $bank = $request->bank;
        if (!$bank) {
            return ['code' => 500, 'msg' => '请选择开户行！'];
        }
        $province = $request->province;
        if (!$province) {
            return ['code' => 500, 'msg' => '请选择开户省份！'];
        }
        $area = DB::table('account_bank')
            ->where('province', $province)
            ->where('bank', $bank)
            ->groupBy('area')
            ->get(['area']);
        return ['code' => 200, 'data' => $area];
    }
    /**
     * 获取开户支行
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bankCodeList(Request $request) {
        $bank = $request->bank;
        if (!$bank) {
            return ['code' => 500, 'msg' => '请选择开户行！'];
        }
        $province = $request->province;
        if (!$province) {
            return ['code' => 500, 'msg' => '请选择开户省份！'];
        }
        $area = $request->area;
        if (!$area) {
            return ['code' => 500, 'msg' => '请选择开户城市！'];
        }
        $bankCode = DB::table('account_bank')
            ->where('province', $province)
            ->where('area', $area)
            ->where('bank', $bank)
            ->get(['name','code']);
        return ['code' => 200, 'data' => $bankCode];
    }
}