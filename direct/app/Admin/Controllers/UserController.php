<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\GrantEdit;
use App\Http\Controllers\Controller;
use App\Models\Users;
use Auth;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Hash;
use Illuminate\Support\MessageBag;

class UserController extends Controller {
	use ModelForm;

	public function checkPassword() {
		$user = Auth::guard('admin')->user();
		if (Hash::check(request('password'), $user->password)) {
			return 'success';
		}
		return 'fail';
	}

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('用户');
			$content->description('列表');

			$content->body($this->grid());
		});
	}

	/**
	 * Edit interface.
	 *
	 * @param $id
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('用户管理');
			$content->description('编辑');
			$content->body($this->form()->edit($id));
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Users::class, function (Grid $grid) {
			$grid->paginate(10);
			$grid->model()->orderBy('id', 'desc');
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
			$grid->disableCreateButton();
			$grid->id('ID')->sortable();
			$grid->column('account', '账号id');
			$grid->column('id_number', '身份证号');
			$grid->column('phone', '电话');
			$grid->column('realname', '姓名');
            $grid->column('level', '用户类型')->display(function ($level) {
                if($level == '服务中心'){
                    return '<span class="label label-success">服务中心</span>';
                }else{
                    return $level;
                }
            });
            $states = [
                'on'  => ['value' => 0, 'text' => '激活', 'color' => 'primary'],
                'off' => ['value' => 1, 'text' => '冻结', 'color' => 'default'],
            ];
            $grid->stoped('用户状态')->switch($states);
			$filename = time();
			$excel = new ExcelExpoter();
			$excel->setAttr(
				['id', '账号id', '电话', '姓名', '类型', '注册时间'],
				['id', 'account', 'phone', 'realname', 'level', 'created_at'],
				$filename);
			$grid->exporter($excel);
			$grid->filter(function ($filter) {

				// 去掉默认的id过滤器
				$filter->disableIdFilter();
				$filter->equal('account', '账号');
				// 在这里添加字段过滤器
				$filter->equal('realname', '姓名');
				$filter->equal('level', '会员类型')->select([
                    1 => '代理商',
                    2=> '服务中心',
                    3=> '经销商',
                    4 => '代理'
                ]);
			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableView();
			});
			$grid->created_at();
			$grid->updated_at();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Users::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->text('account', '账号ID')->rules('required|regex:/^\d+$/|min:7', [
				'regex' => '账号必须全部为数字',
				'min' => '账号不能少于7个字符',
			]);
			$form->text('id_number', '身份证号')->rules('required');
			$form->text('phone', '手机号')->rules('required');
			$form->text('realname', '姓名')->rules('required');
            $form->radio('level', '用户类型')->options([
                '代理商' => '代理商',
                '服务中心'=> '服务中心',
                '经销商'=> '经销商',
                '代理' => '代理'
            ]);
            $states = [
                'on'  => ['value' => 0, 'text' => '激活', 'color' => 'primary'],
                'off' => ['value' => 1, 'text' => '冻结', 'color' => 'default'],
            ];
            $form->switch('stoped', '开通？')->states($states);
			$form->saving(function (Form $form) {
				$user = Users::find($form->model()->id);
				if($user->account != $form->account){
				    $user = Users::where('account',$form->account)->first();
                    if ($user) {
                        $error = new MessageBag([
                            'title' => '账户已存在',
                            'message' => '',
                        ]);
                        return back()->with(compact('error'));
                    }
                }
			});
			$form->display('created_at', '注册时间');
			$form->display('updated_at', '更新时间');
		});
	}
	public function freeze() {
		$user = Users::find(request('id'));
		$user->stoped = 1;
		$user->save();
		$error = new MessageBag([
			'title' => '已冻结',
			'message' => '',
		]);
		return back()->with(compact('error'));
	}

	public function activation() {
		$user = Users::find(request('id'));
		$user->stoped = 0;
		$user->save();
		$success = new MessageBag([
			'title' => '已激活',
			'message' => '',
		]);
		return back()->with(compact('success'));
	}

}
