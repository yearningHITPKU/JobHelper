<?php
/**
 * Created by PhpStorm.
 * User: xww
 * Date: 18-5-12
 * Time: 下午10:59
 */

namespace app\common;

use app\frontend\model\AccessToken;
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

        //详细信息
        //$detailURL = 'http://weixiao.qq.com/apps/school-auth/login?media_id=gh_c5c47de251c1&app_key=116BF40DF1AFB055&redirect_uri=https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/Index/appredirect//appid/sspkukavr6ptmcxdsa/detail/1.html';
        $detailURL = 'http://weixiao.qq.com/apps/school-auth/login?media_id=gh_c5c47de251c1&app_key=116BF40DF1AFB055&redirect_uri=https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/Index/appredirect//appid/sspkuxd5h873wfr6ke/detail/1.html';
        $detailinfo = HttpService::http($detailURL,[]);
        Log::record(json_decode($detailinfo, true));
        Debug::dump(json_decode($detailinfo, true));

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
        $data['appid'] = "wxeccb9b302659eec5";
        $data['secret'] = "992f62451978f18d881903051a794129";

        // 查数据库，判断是否没有access_token
        $atModel = new AccessToken();
        $res = $atModel->select();
        //Debug::dump(json_encode($res, JSON_UNESCAPED_UNICODE));
        $size = sizeof($res);
        //Debug::dump($size);

        $time = time();
        //Debug::dump($time);
        //Debug::dump($time + 6600);

        // 如果没有access_token
        if($size == 0){
            //获取access_token
            $return = HttpService::http("https://api.weixin.qq.com/cgi-bin/token",$data);
            $result = json_decode($return, true);
            session('access_token',$result['access_token']);
            //插入数据库
            $atInsert = new AccessToken;
            $atInsert->token = session('access_token');
            $atInsert->invalid_time = $time + 6600;
            $atInsert->save();
        }else{
            //判断access_token是否超时
            if($res[0]['invalid_time'] < $time){
                // 超时，则重新获取access_token，并更新原来的access_token
                $return = HttpService::http("https://api.weixin.qq.com/cgi-bin/token",$data);
                $result = json_decode($return, true);
                session('access_token',$result['access_token']);
                //Debug::dump(session('access_token'));

                //Debug::dump($res[0]['invalid_time']);
                $atInsert = new AccessToken;
                $atInsert->save([
                    'token'  => session('access_token'),
                    'invalid_time' => $time + 6600
                ],['id' => $res[0]['id']]);
            }else{
                // 没超时，则设置access_token为最新的token
                session('access_token',$res[0]['token']);
                //Debug::dump(session('access_token'));
            }
        }
    }

    public function unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0)
            {    // 两个字节的文字
                //$str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
                $str .= base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }

    // 将UNICODE编码后的内容进行解码，编码后的内容格式：\u56fe\u7247 （原始：图片）
    public function unicode_decode($name)
    {
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches))
        {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++)
            {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0)
                {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code).chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                }
                else
                {
                    $name .= $str;
                }
            }
        }
        return $name;
    }
}