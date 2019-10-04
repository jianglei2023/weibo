<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
            'except'  => ['show','create','store','index','confirmEmail']
        ]);

        $this->middleware('guest',[
            'only' =>['create']
        ]);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    //注册页面
    public function create()
    {
        return view('users.create');
    }

    //显示用户
    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(10);

        return view('users.show',compact('user','statuses'));
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
        //Auth::login($user);

        //发送激活邮件
        $this->sendEmailConfirmationTo($user);

        //闪存信息
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收!');

        return redirect('/');
        //重定向
        //return redirect()->route('users.show',[$user]);
    }

    //编辑用户
    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //更新用户信息
    /*
        User 获取用户ID对应的用户实例
        Request 用户更新的表单数据
    */
    public function update(User $user,Request $request)
    {
        //授权策略 判断更新的是否是当前登录用户
        $this->authorize('update',$user);

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

    //删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }

    //发送激活邮件
    public function sendEmailConfirmationTo($user)
    {


        $view = 'emails.confirm';
        $data = compact('user');
        //$from = 'jiang@lei.com';
        $name = 'JiangLei';
        $to   = $user->email;
        $subject = "感谢注册WeiBo应用！请确认你的邮箱。";

        Mail::send($view,$data,function($message) use ($to,$subject){
            $message->to($to)->subject($subject);
        });
    }


    //激活
    public function confirmEmail($token)
    {

        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜，登录成功！');
        return redirect()->route('users.show',[$user]);

    }
}
