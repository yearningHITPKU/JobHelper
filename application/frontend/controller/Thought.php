<?php

namespace app\frontend\controller;

use app\common\Base;

class Thought extends Base
{
    protected $db;
    public function _initialize()
    {
        parent::_initialize();
        $this->db = new \app\frontend\model\Thought();
    }

    /**
     * 显示实习内推信息列表
     * @return string
     * @throws \think\Exception
     */
    public function index()
    {
        $data = $this->db->getAll();
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 显示实习内推信息详情
     * @return string
     * @throws \think\Exception
     */
    public function detail()
    {
        //$id = input('param.id');
        $id = request()->param('id');
        //$thought_type = input(('param.thought_type'));
        $details = $this->db->getDetail($id);
        //halt($details);
        //halt($thought_type);

        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }

/*    public function ()
    {
        //$id = input('param.id');
        $id = request()->param('id');
        //$thought_type = input(('param.thought_type'));
        $details = $this->db->getDetail($id);
        //halt($details);
        //halt($thought_type);

        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }*/
}
