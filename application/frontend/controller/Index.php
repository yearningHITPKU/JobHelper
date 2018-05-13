<?php
namespace app\frontend\controller;

use app\common\Base;
use app\common\HttpService;
use think\Debug;
use think\Log;
use think\Request;

class Index extends Base
{
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
}
