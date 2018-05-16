<?php

namespace app\admin\controller;

use app\admin\common\Base;
use think\Request;
use app\admin\model\Super;
use think\Session;

class Login extends Base
{

    public function index()
    {
        $this->alreadyLogin();
        return $this->view->fetch('login');
    }


    public function check(Request $request)
    {
        // 设置status
        $status = 0;

        //获取表单数据，保存
        $data = $request->param();
        $userName = $data['username'];
        $password = md5($data['password']);

        // 在super表中进行查询
        $map = ['username'=>$userName];
        $admin = Super::get($map);

        // 用户名和密码分开验证
        if (is_null($admin)){
            $message = "用户名错误";
        }elseif($admin-> password != $password){
            $message = "密码错误";

        } else{
            $status =1;
            $message = "登录成功";

//            $admin -> setInc('login_count');
            $admin -> save(['last_time'=> time()]);

            Session::set('user_id', $userName);
            Session::set('user_info', $data);
        }


        return ['status' => $status,'message' => $message];
    }

    public function logout(){
        Session::delete('user_id');
        Session::delete('user_info');

        $this -> success('注销成功，正在返回', 'login/index');
    }
}
