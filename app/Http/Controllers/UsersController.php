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

    //编辑用户
    public function edit(User $user)
    {
        return view('users.edit',compact('user'));
    }

    //更新用户信息
    /*
        User 获取用户ID对应的用户实例
        Request 用户更新的表单数据

    */
    public function update(User $user,Request $request)
    {

        //表单信息验证
        $this->validate($request,[

            'name'  =>  'required|max:50',
            'password'  =>  'nullable|confirmed|min:6'

        ]);

        $data = [];
        $data['name'] = $request->name;

        //判断是否需要修改密码
        if ($request->password) {
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','更新成功!');
        return redirect()->route('users.show',$user->id);


    }

}
