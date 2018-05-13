<?php

namespace app\frontend\model;

use think\Model;

class Intern extends Model
{
    protected $pk = 'id';
    protected $table = 'interns';
    public function getAll($search)
    {
        $search_industry = [];
        if($search['industry'] != 0) {
            $search_industry = ['industry'=>$search['industry']];
        }
        $res = $this->where('is_allowed', 1)
            ->where(['corp_name'=>['like',"%".$search['company']."%"]])
            ->where(['location'=>['like',"%".$search['city']."%"]])
            ->where($search_industry)
            ->where(['title|detail'=>['like',"%".$search['keyword']."%"]])
            ->order('is_pinned desc,time_publish desc')
            ->field('id,title,time_publish,is_pinned')
            ->paginate(15);
        //halt($res);
        //echo $this->getLastSql();
        return $res;
    }

    public function getDetail($id)
    {
        $this->where('id', $id)->setInc('click_times', 1);
        return $this->where('id',$id)->find();
    }
}
