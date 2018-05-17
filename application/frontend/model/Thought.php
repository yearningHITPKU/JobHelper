<?php

namespace app\frontend\model;

use think\Db;
use think\Model;

class Thought extends Model
{
    protected $pk = 'id';
    protected $table = 'thoughts';
    public function getThought($thought_type)
    {
        $res = $this->where('type', $thought_type)
            ->order('time_publish desc')
            ->paginate(15);
        //halt($res);
        return $res;
    }
    public function getDetail($id,$user_id)
    {
        $this->where('id', $id)->setInc('click_times', 1);
        $res1 = $this->where('id',$id)->find();
        $res2 = Db::name('collection')
            ->where('user_id',$user_id)
            ->where('target_id',$id)
            ->where('target_type',1)
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
        //return $this->where('id',$id)->find();
    }

    public function getAll()
    {
        $res = Db::query('select id,title,time_publish from thoughts ORDER BY time_publish DESC');
        //halt($res);
        return $res;
    }

    public function getUserThought($user_id)
    {
        //$res = Db::query('select id,title,time_publish from thoughts where owner_id=? ORDER BY time_publish DESC', [$user_id]);
        $res = $thoughts = Db::name('thoughts')
            ->where('owner_id', $user_id)
            ->order('time_publish desc')
            ->field('id,title,corp_name,position,time_publish')
            ->select();
        return $res;
    }

    public function getSearch($search)
    {
        $res = $this->where(['owner_id|title|corp_name|position|detail|owner_name'=>['like',"%".$search."%"]])
            ->order('time_publish desc')
            ->field('id,title,corp_name,position,time_publish')
            ->select();
        return $res;
    }
}
