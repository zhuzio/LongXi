<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\MessageBag;

class SoftwareController extends Controller
{
    public function index()
    {
        return Admin::content(function(Content $content) {
            $content->row(function ($row) {
                $tab = new Tab();

                $aboutus = new Form();
                $aboutus->action('introduction');
                $aboutus->hidden('key')->default('aboutus');
                $aboutus->textarea('aboutus', '关于我们')->default($this->getContent('aboutus'));

                $license = new Form();
                $license->action('introduction');
                $license->hidden('key')->default('license');
                $license->textarea('license', '软件使用条款')->default($this->getContent('license'));

                $userhelp = new Form();
                $userhelp->action('introduction');
                $userhelp->hidden('key')->default('userhelp');
                $userhelp->textarea('userhelp', '用户帮助')->default($this->getContent('userhelp'));

                $tab->add('关于我们', $aboutus);
                $tab->add('软件使用条款', $license);
                $tab->add('用户帮助', $userhelp);

                $row->column(12, $tab);
            });
        });
    }

    private function getContent($key)
    {
        return @file_get_contents(resource_path() . '/introduction/' . $key);
    }

    public function save()
    {
        $key = request('key');
        if (!in_array($key, ['aboutus', 'license', 'userhelp'])) {
            return;
        }
        $content = request($key);
        $file = resource_path() . '/introduction/' . $key;
        file_put_contents($file, $content);

        $success = new MessageBag([
            'title' => '修改成功',
            'message' => '',
        ]);
        return back()->with(compact('success'));
    }
}