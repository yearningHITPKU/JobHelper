<?php

namespace app\frontend\model;

use think\Model;
use think\Db;

class User extends Model
{
    public function insertStudent($user_id, $user_name)
    {
        //若数据库中不存在此用户,则插入数据库
        if(!Db::name('user')->where('id', $user_id)->find()) {
            Db::name('user')->insert(['id'=>$user_id, 'name'=>$user_name]);
        }
    }
}
