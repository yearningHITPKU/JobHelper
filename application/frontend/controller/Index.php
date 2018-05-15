<?php
namespace app\frontend\controller;

use app\common\Base;
use app\common\HttpService;
use app\frontend\model\Intern;
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
        //$this->get_access_token();
        $this->get_user_data();

        $access_token = session('access_token');
        Log::record($access_token);

        $data = [];
        $data['scene'] = "asda&asdsad";
        $data['page'] = "pages/pushInfo/pushInfo";
        $json = json_encode($data);
        //$data['width'] = 430;
        //$data['auto_color'] = false;
        //$data['line_color'] = "";
        //$data['is_hyaline'] = "";

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
        $output = HttpService::http($url, [], $json, "POST");
        $qrcode = json_decode($output, true);
        Log::record($qrcode);

        $this->assign('user_name', session('user_name'));
        $this->assign('user_id', session('user_id'));

        //return 'app\frontend\controller\Index:'.'<br>'.session('user_id')."<br>".session('user_name')."<br>'".$access_token;
        return $this->fetch();
    }

    public function intern()
    {
        $interns = Db::name('interns')
            ->where('is_allowed', 1)
            ->order('time_publish desc')
            ->field('id,title,time_publish')
            ->select();
            //->paginate(10);

        return json_encode($interns, JSON_UNESCAPED_UNICODE);
    }

    public function detail()
    {
        $id = request()->param('id');
        Log::record($id);
        $interns = $this->db->getDetail($id);

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
        $detail = request()->param('detail');
        $grade = request()->param('grade');
        $location = request()->param('location');
        $position = request()->param('position');
        $corp_name = request()->param('corp_name');
        $title = request()->param('title');
        $type = request()->param('purpose');
        $owner = request()->param('owner_name');

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
        $intern->save();

        return json_encode($intern, JSON_UNESCAPED_UNICODE);
    }
}
