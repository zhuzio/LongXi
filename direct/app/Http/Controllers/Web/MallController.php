<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MallController extends Controller
{
    public function shopCenter(){
        $list = Product::where('on_sale',1)
            ->orderBy('sales','desc')
            ->paginate(30);
        foreach ($list as &$v) {
            $v['pic'] = '/upload/'.$v['pic'];
        }
        unset($v);
        return view('web.mall.shopCenter',['data'=>$list]);
    }

}
