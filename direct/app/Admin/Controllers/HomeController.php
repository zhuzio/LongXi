<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use App\Models\Users;
use DB;
class HomeController extends Controller {
	public function index() {
		return Admin::content(function (Content $content) {
            $user_data = Users::select(DB::raw('sum(level=1) as common_user_count, sum(level=2) as center_count'))
                ->first();
            $repeat_consum = DB::table('assets')->sum('repeat_consum');
            $withdraw = DB::table('apply_withdraw')->sum('real_money');
			$content->header('数据统计');
			$content->description('');

			$content->row(function (Row $row) use ($user_data,$repeat_consum,$withdraw) {
				$row->column(3, new InfoBox('代理商', 'users', 'aqua', '/admin/users', $user_data->common_user_count));
				$row->column(3, new InfoBox('报单中心', 'users', 'green', '/admin/users', $user_data->center_count));
				$row->column(3, new InfoBox('重复消费', 'jpy', 'yellow', '#', $repeat_consum));
				$row->column(3, new InfoBox('提现总额', 'jpy', 'red', '#', $withdraw));
			});
			$content->body(view('admin.charts.bar'));
		});
	}
}
