<?php
use Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//调试
Route::get('/log', function () {
	$date = request('date');
	$log_file = storage_path() . '/logs/laravel-' . $date . '.log';
	if (file_exists($log_file)) {
		$log = file_get_contents($log_file);
		return str_replace(["\r\n", "\n"], "<br>", $log);
	}
	return 'not found log';
});
//调试
Route::get('/test', function () {
    Log::info('test');
});
Route::group(['namespace' => 'Web'], function () {

	Route::get('/advertPointsReturn', 'AutoController@advertPointsReturn');
	Route::get('/testCapital', 'HomeController@testCapital');
	Route::get('/login', 'UserController@login');
	Route::post('/login', 'UserController@login');
	Route::get('/loginOut', 'UserController@loginOut');
    Route::any('user/sendSms', 'UserController@sendSms'); //注册验证码
    Route::any('user/checkSmsCode', 'UserController@checkSmsCode'); //注册验证码 校验
	Route::get('/register', 'UserController@register');
	Route::post('/register', 'UserController@register');

});

Route::group(['namespace' => 'Web', 'middleware' => ['auth.session']], function () {
	Route::get('/', 'HomeController@index');
	Route::get('/index', 'HomeController@index');
	Route::get('/welcome', 'HomeController@welcome');
	//转让控制器
	Route::get('/electronicTransfer', 'TransferController@electronicTransfer');
	Route::post('/electronicTransfer', 'TransferController@electronicTransfer');
	Route::get('/shopPointsTransfer', 'TransferController@shopPointsTransfer');
	Route::post('/shopPointsTransfer', 'TransferController@shopPointsTransfer');
	Route::post('/transfer/checkUser', 'TransferController@checkUser');

	Route::get('/transferList', 'TransferController@transferList');
	//转换控制器
	Route::get('/transformAddedPoints', 'TransformController@transformAddedPoints');
	Route::post('/transformAddedPoints', 'TransformController@transformAddedPoints');
	Route::get('/transformAddedPointsList', 'TransformController@transformAddedPointsList');
	Route::get('/transformElectronic', 'TransformController@transformElectronic');
	Route::post('/transformElectronic', 'TransformController@transformElectronic');
	Route::get('/transformElectronicList', 'TransformController@transformElectronicList');
	//报单中心审核控制器
	Route::get('/waitCheck', 'CheckController@waitCheck');
	Route::get('/agree/{uid}', 'CheckController@agree');
	Route::post('/agree', 'CheckController@agree');
    Route::get('/checkLog', 'CheckController@checkLog');

	//申请报单中心控制器
	Route::get('/apply', 'ApplyController@apply');
    Route::post('/apply', 'ApplyController@apply');
	//申请提现控制器
	Route::get('/applyWithdraw', 'WithdrawController@applyWithdraw');
	Route::post('/applyWithdraw', 'WithdrawController@applyWithdraw');
	Route::get('/withdrawList', 'WithdrawController@withdrawList');
	//会员信息
	Route::get('/resetPassword', 'UserController@resetPassword');
	Route::post('/resetPassword', 'UserController@resetPassword');
	Route::get('/resetPayment', 'UserController@resetPayment');
	Route::post('/resetPayment', 'UserController@resetPayment');
	Route::get('/myRecommend', 'UserController@myRecommend');
    Route::get('/centerRecommend', 'UserController@centerRecommend');
	Route::post('/checkUser', 'UserController@checkUser');
    Route::get('/feedback', 'UserController@feedback');
	//会员财务
	Route::get('/assets', 'AssetsController@assets');
	Route::post('/assets', 'AssetsController@assets');
	Route::get('/assets', 'AssetsController@assets');
	Route::get('/dynamicLog', 'AssetsController@dynamicLog');
	Route::get('/staticLog', 'AssetsController@staticLog');
	Route::get('/shopPointsLog', 'AssetsController@shopPointsLog');

	//购物中心
	Route::get('/mall/shopCenter', 'MallController@shopCenter');

	//线上订单
	Route::post('/order/create', 'OrderController@create');
	Route::post('/order/waitpay', 'OrderController@waitpay');
	Route::get('/order/waitExpress', 'OrderController@waitExpress');

	Route::get('/order/waitreceive', 'OrderController@waitreceive');
	Route::post('/order/orderReceive', 'OrderController@orderReceive');
	//银行卡
	Route::get('/banklist', 'BankController@banklist');
	Route::post('/banksList', 'BankController@banksList');
	Route::post('/addBankCard', 'BankController@addBankCard');
	Route::post('/bankProvince', 'BankController@bankProvince');
	Route::post('/bankCity', 'BankController@bankCity');
	Route::post('/bankCodeList', 'BankController@bankCodeList');
	//规则说明
    Route::get('/userhelp', 'IntroductionController@userhelp');
    Route::get('/aboutus', 'IntroductionController@aboutus');

});
