<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{

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
        if (Auth::attempt($rcedentials)) {

            session()->flash('success','欢迎回来!');
            return redirect()->route('users.show',[Auth::user()]);

        }else{

            session()->flash('danger','抱歉，您的邮箱或密码不匹配');
            return redirect()->back()->withinput();

        }


    }



}
