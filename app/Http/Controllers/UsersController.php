<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
class UsersController extends Controller
{
    //注册页面
    public function create()
    {
        return view('users.create');
    }

    //显示用户
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    //用户注册
    public function store(Request $request)
    {

        //表单信息验证
        $this->validate($request,[
            'name'  =>  'required|max:50',
            'email' =>  'required|email|unique:users|max:255',
            'password'  =>  'required|confirmed|min:6'
        ]);

        //创建用户
        $user = User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'password'=>bcrypt($request->password),
        ]);

        //直接登录
        Auth::login($user);

        //闪存信息
        session()->flash('success','欢迎注册!');
        //重定向
        return redirect()->route('users.show',[$user]);
    }


}
