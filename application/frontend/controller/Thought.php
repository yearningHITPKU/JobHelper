<?php

namespace app\frontend\controller;

use app\common\Base;
use think\Db;


class Thought extends Base
{
    protected $db;
    public function _initialize()
    {
        parent::_initialize();
        $this->db = new \app\frontend\model\Thought();
    }

    public function index()
    {
        $pageindex = request()->param('pageIndex');
        $callbackcount = request()->param('callBackCount');

        $thoughtsAll = Db::name('thoughts')
            ->order('time_publish desc')
            ->field('id,title,corp_name,position,time_publish')
            ->select();
        $thoughts = array_slice($thoughtsAll, ($pageindex-1)*$callbackcount, $callbackcount);

        //$data = $this->db->getAll();

        //$result = array_slice($data, ($pageindex-1)*$callbackcount, $callbackcount);

        return json_encode($thoughts, JSON_UNESCAPED_UNICODE);
    }

    public function detail()
    {
        $id = request()->param('id');
        $user_id = request()->param('user_id');
        $details = $this->db->getDetail($id,$user_id);
        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }

    public function mythought()
    {
        $id = request()->param('user_id');
        $details = $this->db->getUserThought($id);

        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }

    public function publish()
    {
        $user_id = request()->param('user_id');
        $owner = request()->param('owner_name');
        $title = request()->param('title');
        $corp_name = request()->param('corp_name');
        $position = request()->param('position');
        $detail = request()->param('detail');

        $user = Db::name('user')
            ->where('uid',$user_id)
            ->find();

        $thought = new \app\frontend\model\Thought();
        $thought->detail = $detail;
        $thought->corp_name = $corp_name;
        $thought->position = $position;
        $thought->title = $title;
        $thought->owner_id = $user['id'];
        //$thought->owner_name = $owner;
        $thought->save();

        return json_encode($thought, JSON_UNESCAPED_UNICODE);
    }

    public function search()
    {
        $data = $this->db->getSearch(input('keyword'));
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
