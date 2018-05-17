<?php

namespace app\frontend\controller;

use app\common\Base;
use app\frontend\model\User;
use think\Debug;

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

        Debug::dump($isCollected);
        if($isCollected){
            $collection = new \app\frontend\model\Collection();
            $collection->user_id = $user_id;
            $collection->target_id = $target_id;
            $collection->target_type = $target_type;
            $collection->save();
        }else{
            User::destroy(['user_id'=>$user_id, 'target_id'=>$target_id, 'target_type'=>$target_type]);
        }
        //return json_encode($collection, JSON_UNESCAPED_UNICODE);
    }

    public function get_my_collection()
    {

    }
}
