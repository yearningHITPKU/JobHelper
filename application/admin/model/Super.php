<?php

namespace app\admin\model;

use think\Model;

class Super extends Model
{
    //主键
    protected $pk = 'id';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'super';

    //获取器函数，将时间戳格式化为时间
    public function getLastTimeAttr($val){
        return date('Y/m/d',$val);
    }

    //修改器函数，将密码用md5存储
    public function setPasswordAttr($val){
        return md5($val);
    }
}
