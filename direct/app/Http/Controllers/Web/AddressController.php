<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use DB;
use Validator;


class AddressController extends Controller
{
    public function index(Request $request)
    {
        $list = Address::where('uid', $request->user->id)->get();
        return [
            'code' => 200,
            'data' => $list
        ];
    }

    public function addressinfo(Request $request)
    {
        $address = Address::where('uid', $request->user->id)
            ->where('id', $request->address_id)
            ->first();

        return [
            'code' => 200,
            'data' => $address
        ];
    }

    public function add(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'phone' => 'required|regex:/^1[34578][0-9]{9}$/',
                'province' => 'required',
                'city' => 'required',
                'country' => 'required',
                'detail' => 'required',
            ],
            [
                'phone.required' => '缺少手机号',
                'phone.regex' => '手机格式不正确'
            ]
        );


        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'code' => 500,
                'message' => $errors[0],
            ];
        }
        $has_address = Address::where('uid', $request->user->id)
            ->count();
        Address::create([
            'uid' => $request->user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'country' => $request->country,
            'detail' => $request->detail,
            'is_default' => $has_address ? 0 : 1,
        ]);

        return [
            'code' => 200,
            'message' => '新增成功',
        ];
    }

    public function edit(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'name' => 'required',
                'phone' => 'required|regex:/^1[34578][0-9]{9}$/',
                'province' => 'required',
                'city' => 'required',
                'country' => 'required',
                'postcode' => 'required',
                'detail' => 'required',
            ],
            [
                'phone.required' => '缺少手机号',
            ]
        );


        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'code' => 500,
                'message' => $errors[0],
            ];
        }
        Address::where('id', $request->id)
            ->where('uid', $request->user->id)
            ->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'province' => $request->province,
                'city' => $request->city,
                'country' => $request->country,
                'postcode' => $request->postcode,
                'detail' => $request->detail
            ]);

        return [
            'code' => 200,
            'message' => '修改成功',
        ];
    }

    public function setDefault(Request $request)
    {
        Address::where('uid', $request->user->id)
            ->where('id', $request->id)
            ->update(['is_default' => 1]);

        Address::where('uid', $request->user->id)
            ->where('id', '<>', $request->id)
            ->update(['is_default' => 0]);


        return [
            'code' => 200,
            'message' => '修改成功',
        ];
    }

    public function delAddress(Request $request)
    {
        Address::where('uid', $request->user->id)
            ->where('id', $request->id)
            ->delete();

        return [
            'code' => 200,
            'message' => '删除成功',
        ];
    }
}