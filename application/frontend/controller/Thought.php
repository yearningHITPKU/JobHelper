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

    public function index()
    {
        $data = $this->db->getAll();
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function detail()
    {
        $id = request()->param('id');
        $details = $this->db->getDetail($id);

        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }

    public function mythought()
    {
        $id = request()->param('user_id');
        $details = $this->db->getUserThought($id);

        return json_encode($details, JSON_UNESCAPED_UNICODE);
    }
}
