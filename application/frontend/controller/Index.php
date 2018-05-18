<?php
namespace app\frontend\controller;

use app\common\Base;
use app\frontend\model\Intern;
use think\Debug;
use think\Log;
use think\Request;
use think\Db;

class Index extends Base
{
    protected $db;
    public function _initialize()
    {
        parent::_initialize();
        $this->db = new \app\frontend\model\Intern();
    }

    public function index(Request $request)
    {
        // 每次用户进入时,判断是否需要重新获取access_token
        $this->get_access_token();
        // 获取用户数据
        $this->get_user_data();
        /*session('user_id','1701210926');
        session('user_name','向往');*/
/*
        $access_token = session('access_token');
        Log::record($access_token);

        // 获取用户专用的小程序码
        $fileName = 'static/qrcode/'.session('user_id').'data.jpg';
        if(is_file($fileName)){
            // 存在该用户的小程序码，则直接读取
            //Debug::dump("该用户小程序码已经存在");
        }else{
            // 不存在该用户的小程序码，则线获取并生成小程序码，再读取
            $data = [];
            $data['scene'] = session('user_id').'&'.$this->unicode_encode(session('user_name'));
            //$data['path'] = 'pages/pushInfo/pushInfo';
            //Debug::dump($data['scene']);
            $data['page'] = "pages/pushInfo/pushInfo";
            $json = json_encode($data);
            //$data['width'] = 430;
            //$data['auto_color'] = false;
            //$data['line_color'] = "";
            $data['is_hyaline'] = true;

            $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
            $response = HttpService::http($url, [], $json, "POST");
            $result = json_decode($response, true);
            Log::record($response);
            Log::record($result);

            // 存储用户特定的二维码
            $image   = "static/qrcode/noimg.jpg"; //图片地址
            $fp      = fopen($image, 'rb');
            $content = fread($fp, filesize($image)); //二进制数据
            $ok = file_put_contents('static/qrcode/'.session('user_id').'data.jpg', $content);
        }*/

        // 传递给页面的数据
        $this->assign('user_name', session('user_name'));
        $this->assign('user_id', session('user_id'));
        $this->assign('user_qrcode', 'static/qrcode/qrcode.jpg');
        //$this->assign('user_qrcode', $fileName);

        return $this->fetch();
    }

    public function intern()
    {
        $interns = Db::name('interns')
            ->where('is_allowed', 1)
            ->order('time_publish desc')
            ->field('id,title,time_publish,location,position,salary')
            ->select();
            //->paginate(10);

        return json_encode($interns, JSON_UNESCAPED_UNICODE);
    }

    public function detail()
    {
        $id = request()->param('id');
        $user_id = request()->param('user_id');
        Log::record($id);
        $interns = $this->db->getDetail($id,$user_id);

        return json_encode($interns, JSON_UNESCAPED_UNICODE);
    }

    public function myIntern()
    {
        $id = request()->param('user_id');
        $data = $this->db->getUserIntern($id);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function publish()
    {
        $user_id = request()->param('user_id');
        $owner = request()->param('owner_name');
        $detail = request()->param('detail');
        $grade = request()->param('grade');
        $location = request()->param('location');
        $position = request()->param('position');
        $corp_name = request()->param('corp_name');
        $title = request()->param('title');
        $type = request()->param('purpose');
        $salary = request()->param('salary');

        /*$data= request()->param();
        Log::record($data['data']['data']['num']);*/

        $intern = new Intern;
        $intern->detail = $detail;
        $intern->grade = $grade;
        $intern->location = $location;
        $intern->position = $position;
        $intern->title = $title;
        $intern->corp_name = $corp_name;
        $intern->type = $type;
        $intern->owner_id = $user_id;
        $intern->owner_name = $owner;
        $intern->salary = $salary;
        //$intern->time_publish = date("Y-m-d H:i",time());
        $intern->save();

        return json_encode($intern, JSON_UNESCAPED_UNICODE);
    }

    public function intern_thought()
    {
        $interns = Db::name('interns')
            ->where('is_allowed', 1)
            ->order('time_publish desc')
            ->field('id,title,time_publish,location,position,salary')
            ->select();

        $thoughts = Db::name('thoughts')
            ->order('time_publish desc')
            ->field('id,title,corp_name,position,time_publish')
            ->select();

        $both = array($interns,$thoughts);
        return json_encode($both, JSON_UNESCAPED_UNICODE);
    }

    public function my_intern_thought()
    {
        $id = request()->param('user_id');
        $interns = Db::name('interns')
            ->where('owner_id', $id)
            ->order('time_publish desc')
            ->field('id,title,time_publish,location,position,salary')
            ->select();
        //$interns = $this->db->getUserIntern($id);
        //$thoughts = Db::query('select * from thoughts where owner_id=? ORDER BY time_publish DESC', [$id]);
        $thoughts = Db::name('thoughts')
            ->where('owner_id', $id)
            ->order('time_publish desc')
            ->field('id,title,corp_name,position,time_publish')
            ->select();
        $both = array($interns,$thoughts);
        return json_encode($both, JSON_UNESCAPED_UNICODE);
    }

    public function search()
    {
        $data = $this->db->getAll(input('keyword'));
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function delete()
    {
        $user_id = request()->param('user_id');
        $id = request()->param('id');
        $currentTab = request()->param('currentTab');

        $res = 0;
        if($currentTab == 0){
            $res = Intern::destroy($id);
        }elseif ($currentTab == 1){
            $res = \app\frontend\model\Thought::destroy($id);
        }/*elseif ($currentTab == 2){
            $res = \app\frontend\model\Collection::destroy(['user_id'=>$user_id, 'target_id'=>$id, 'target_type'=>0]);
        }elseif ($currentTab == 3){
            $res = \app\frontend\model\Collection::destroy(['user_id'=>$user_id, 'target_id'=>$id, 'target_type'=>1]);
        }*/

        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}
