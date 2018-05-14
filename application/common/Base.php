<?php
/**
 * Created by PhpStorm.
 * User: xww
 * Date: 18-5-12
 * Time: 下午10:59
 */

namespace app\common;

use think\Controller;
use think\Debug;
use think\Log;

class Base extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function get_user_data()
    {
        Log::record(session('user_id'));

        /*//详细信息
        $detailURL = 'http://weixiao.qq.com/apps/school-auth/login?media_id=gh_c5c47de251c1&app_key=116BF40DF1AFB055&redirect_uri=https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/Index/appredirect//appid/sspkukavr6ptmcxdsa/detail/1.html';
        $detailinfo = HttpService::http($detailURL,[]);
        Log::record(json_decode($detailinfo, true));
        Debug::dump(json_decode($detailinfo, true));*/

        if(!session('user_id')) {
            //获得加密的rawdata
            $getdata = input('get.');
            if(!$getdata || !isset($getdata['rawdata'])) {
                $this->error("请通过微信校园卡登录");
            } else {
                $rawdata = $getdata['rawdata'];
                //设置post数据
                $data = [];
                $data['appid'] = 'sspkukavr6ptmcxdsa';
                $data['appsecret'] = '0c33bbcfc0c41af6b26748b7467e6e8e';
                $data['content'] = $rawdata;
                $output = HttpService::http("https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/OpenApi/decode", [], $data, "POST");
                $userdata = json_decode($output, true);
                if ($userdata['status'] == 1) {
                    //设置session
                    $sessionList = [
                        'gender', 'type_name', 'degree_type_name', 'researcharea',
                        'major_name', 'mail', 'telephone', 'birthday', 'politicsstatus', 'nation',
                        'location', 'teacher_id', 'teacher_name', 'vice_teacher_id', 'vice_teacher_name',
                        'startyearmonth', 'grade', 'hktwmacn', 'domain', 'imgurl'
                    ];
                    session('user_id', isset($userdata['data']['card_number']) ? $userdata['data']['card_number'] : $userdata['data']['studentid']);
                    session('user_name', $userdata['data']['name']);
                    foreach ($sessionList as $item) {
                        session('user_' . $item, isset($userdata['data'][$item]) ? $userdata['data'][$item] : "");
                    }
                    //用户信息写入数据库
                    (new \app\frontend\model\User())->insertStudent($userdata['data']['card_number'], $userdata['data']['name']);
                } else {
                    $this->error('请求连接失败');
                }
            }
        }
    }

    public function get_access_token()
    {
        $data = [];
        $data['grant_type'] = "client_credential";
        $data['appid'] = "wx87a61a8e29066d1f";
        $data['secret'] = "b40d22eaf4c41acc3a2bf328a2174b9a";

        $return = HttpService::http("https://api.weixin.qq.com/cgi-bin/token",$data);
        $result = json_decode($return, true);

        session('access_token',$result['access_token']);
    }
}