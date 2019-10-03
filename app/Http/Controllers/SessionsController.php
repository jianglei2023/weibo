<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{

    public function __construct()
    {

        //未登录用户能访问的页面
        $this->middleware('guest',[
            'only'  => ['create']
        ]);

    }

    //登录页面
    public function create()
    {

        return view('sessions.create');
    }


    //用户登录
    public function store(Request $request)
    {
        //验证表单数据
        $rcedentials = $this->validate($request,[
            'email' =>  'required|email|max:255',
            'password'  =>  'required'
        ]);

        //用户认证
        if (Auth::attempt($rcedentials,$request->has('remeber'))) {

            session()->flash('success','欢迎回来!');

            $fallback = route('users.show',[Auth::user()]);
            return redirect()-> intended($fallback);

        }else{

            session()->flash('danger','抱歉，您的邮箱或密码不匹配');
            return redirect()->back()->withinput();

        }


    }

    //用户退出
    public function destory()
    {
        Auth::logout();
        session()->flash('success','您已退出!');
        return redirect('login');
    }



}
