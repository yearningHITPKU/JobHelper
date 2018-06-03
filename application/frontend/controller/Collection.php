<?php

namespace app\frontend\controller;

use app\common\Base;
use think\Debug;
use think\Db;

class Collection extends Base
{
    protected $db;
    public function _initialize()
    {
        parent::_initialize();
        $this->db = new \app\frontend\model\Collection();
    }

    public function index()
    {
        $user_id = request()->param('user_id');
        $target_id = request()->param('target_id');
        $target_type = request()->param('currentTab');
        $isCollected = request()->param('isCollected');

        //Debug::dump($isCollected);
        if($isCollected){
            $user = Db::name('user')
                ->where('uid',$user_id)
                ->find();

            $collection = new \app\frontend\model\Collection();
            $collection->user_id = $user['id'];
            $collection->target_id = $target_id;
            $collection->target_type = $target_type;
            $collection->save();
        }else{
            \app\frontend\model\Collection::destroy(['user_id'=>$user_id, 'target_id'=>$target_id, 'target_type'=>$target_type]);
        }
        //return json_encode($collection, JSON_UNESCAPED_UNICODE);
    }

    public function get_my_collection()
    {
        $user_id = request()->param('user_id');

        $c_interns = Db::name('collection')
            ->alias('c')
            ->join('user u', 'u.id=c.user_id')
            ->where('u.uid', $user_id)
            ->join('interns i', 'i.id = c.target_id')
            ->where('i.is_allowed', 1)
            ->order('time_publish desc')
            ->field('i.id,i.title,i.time_publish,i.location,i.position,i.salary,c.target_id')
            ->select();

        $c_thoughts = Db::name('collection')
            ->alias('c')
            ->join('user u', 'u.id=c.user_id')
            ->where('u.uid', $user_id)
            ->join('thoughts t', 'c.target_id = t.id')
            ->order('t.time_publish desc')
            ->field('t.id,t.title,t.corp_name,t.position,t.time_publish,c.target_id')
            ->select();

        $both = array($c_interns,$c_thoughts);
        return json_encode($both, JSON_UNESCAPED_UNICODE);
    }
}
