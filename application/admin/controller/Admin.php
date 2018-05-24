<?php

namespace app\admin\controller;
use app\admin\common\Base;
use app\admin\model\Super;
use think\Request;

class Admin extends Base
{
    public function index()
    {
        //1.读取super表数据‘
        $admin = Super::get(['username'=>'admin']);
        //2.将当前管理员信赋值给模板
        $this->view->assign('admin', $admin);
        //3.渲染模板
        return $this->view->fetch('admin_list');
    }

    public function edit()
    {
        return $this->view->fetch('admin_edit');
    }
}
