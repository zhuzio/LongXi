<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
	'prefix' => config('admin.route.prefix'),
	'namespace' => config('admin.route.namespace'),
	'middleware' => config('admin.route.middleware'),
], function (Router $router) {
	$router->get('/', 'HomeController@index');
	$router->put('/{id}', function () {
		return response()->json([
			'status' => true,
			'message' => '更新成功 !',
		]);
	});
	$router->resource('users', UserController::class);
	$router->resource('products', ProductController::class);
	$router->resource('config', ConfigController::class);
	$router->resource('finance', FinanceController::class);
	$router->resource('categories', CategoryController::class, ['except' => ['create']]);
	$router->get('/reportCenter/waitCheck', 'ReportCenterController@waitCheck');
	$router->get('/reportCenter/agreeList', 'ReportCenterController@agreeList');
	$router->get('/reportCenter/refuseList', 'ReportCenterController@refuseList');
	$router->post('/reportCenter/agree', 'ReportCenterController@agree');
	$router->post('/reportCenter/refuse', 'ReportCenterController@refuse');
	$router->get('/user/freeze', 'UserController@freeze');
	$router->get('/user/activation', 'UserController@activation');
	$router->get('/assets/users', 'AssetsController@users');
	$router->get('/dynamic/income', 'DynamicController@income');
	$router->get('/static/income', 'StaticController@income');
	$router->get('/introduction', 'SoftwareController@index');
	$router->post('/introduction', 'SoftwareController@save');
	$router->get('/order/waitExpress', 'OrderController@waitExpress');
	$router->get('/order/waitreceive', 'OrderController@waitreceive');
    $router->get('/redOrderDetail/{id}', 'OrderController@redOrderDetail');
    $router->get('/order/waitExpress/{order}/edit', 'OrderController@edit');
    $router->put('/order/waitExpress/{order}', 'OrderController@update');
    $router->get('/order/waitreceive/{order}/edit', 'OrderController@edit');
    $router->put('/order/waitreceive/{order}', 'OrderController@update');
    $router->get('/withdraw/waitCheck', 'WithdrawController@waitCheck');
    $router->get('/withdraw/withdrawList', 'WithdrawController@withdrawList');
    $router->post('/withdraw/confirmPayment', 'WithdrawController@confirmPayment');
    $router->post('/withdraw/refusePayment', 'WithdrawController@refusePayment');
    $router->get('/transfer/index', 'TransferController@index');
});
