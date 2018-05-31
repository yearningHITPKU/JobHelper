<?php

namespace app\frontend\model;

use think\Model;
use think\Db;

class Intern extends Model
{
    protected $pk = 'id';
    protected $table = 'interns';

    public function getAll($search)
    {
        $res = $this->where('is_allowed', 1)
            ->where(['title|corp_name|location|detail|salary|owner_id'=>['like',"%".$search."%"]])
            ->order('is_pinned desc,time_publish desc')
            ->field('id,title,time_publish,location,position,salary,is_pinned')
            ->select();
        return $res;
    }

    public function getDetail($id,$user_id)
    {
        // 点击次数加1
        $this->where('id', $id)->setInc('click_times', 1);
        /*$res = Db::table('interns')
            ->alias('i')
            ->where('i.id',$id)
            ->join('collection c', 'i.id = c.target_id')
            ->where('c.user_id',$user_id)
            ->where('c.target_type',0)
            ->field('')
            ->select();*/
        //$res1 = $this->where('id',$id)->find();
        $res1 = $this->alias('i')
            ->join('__USER__ u', 'i.owner_id=u.id')
            ->where('i.id', $id)
            ->field('i.*, u.name as owner_name')
            ->find();
        $res2 = Db::name('collection')
            ->where('user_id',$user_id)
            ->where('target_id',$id)
            ->where('target_type',0)
            ->select();
        $isCollected = false;
        if(sizeof($res2) == 0){
            $isCollected = false;
        }else{
            $isCollected = true;
        }

        $res['data'] = $res1;
        $res['isCollected'] = $isCollected;
        //return $this->where('id',$id)->find();
        return $res;
    }

    public function getUserIntern($user_id)
    {
        $res = Db::query('select * from interns where owner_id=? and is_allowed=? ORDER BY time_publish DESC', [$user_id, 1]);
        return $res;
    }
}
