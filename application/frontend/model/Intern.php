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
            ->where(['title|corp_name|location|detail|salary|owner_id|owner_name'=>['like',"%".$search."%"]])
            ->order('is_pinned desc,time_publish desc')
            ->field('id,title,time_publish,location,position,salary,is_pinned')
            ->select();
        return $res;
    }

    public function getDetail($id)
    {
        $this->where('id', $id)->setInc('click_times', 1);
        return $this->where('id',$id)->find();
    }

    public function getUserIntern($user_id)
    {
        $res = Db::query('select * from interns where owner_id=? and is_allowed=? ORDER BY time_publish DESC', [$user_id, 1]);
        return $res;
    }
}
