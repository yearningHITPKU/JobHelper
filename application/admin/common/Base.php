<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/8
 * Time: 14:58
 */
namespace app\admin\common;
use think\Controller;
use think\Session;

class Base extends Controller
{

    protected function _initialize()
    {
        parent::_initialize();
        define('USER_ID', Session::get('user_id'));
    }

    protected function isLogin(){
        if (is_null(USER_ID)){
            $this -> error('未登录', 'login/index');
        }
    }

    protected function alreadyLogin(){
        if (!is_null(USER_ID)){
            $this->error('已经登录，无需再登','index/index');
        }
    }

}