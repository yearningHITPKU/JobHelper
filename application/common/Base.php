<?php
/**
 * Created by PhpStorm.
 * User: xww
 * Date: 18-5-12
 * Time: 下午10:59
 */

namespace app\common;

use think\Controller;
use think\Db;
use think\Debug;

class Base extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
/*
        $accessToken = $this->get_access_token();
        Debug::dump('asdsadsadsadasd: '.$accessToken);

        if(!session('user_id')) {
            //获得加密的rawdata
            $getdata = input('get.');
            if(!$getdata || !isset($getdata['rawdata'])) {
                $this->error("请通过微信校园卡登录");
            } else {
                $rawdata = $getdata['rawdata'];
                //设置post数据
                $data = [];
                $data['appid'] = Db::name('webset')->where('name', 'appid')->column('value')[0];
                $data['appsecret'] = Db::name('webset')->where('name', 'appsecret')->column('value')[0];
                $data['content'] = $rawdata;

                //post请求获得用户信息
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/OpenApi/decode");
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                if (! empty($data)) {
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                $output = curl_exec($curl);
                $userdata = json_decode($output, true);
                if($userdata['status'] == 1) {
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
                        session('user_'.$item, isset($userdata['data'][$item]) ? $userdata['data'][$item] : "");
                    }
                    //暂时不知道怎么分辨是校友还是在校生,都先设置为在校生
                    $this->setUserType();
                    //用户信息写入数据库
                    if(session('user_type') == 0) {
                        (new \app\frontend\model\Login())->insertStudent($userdata['data']['card_number'], $userdata['data']['name']);
                    } else {
                        (new \app\frontend\model\Login())->insertGraduate($userdata['data']['card_number'], $userdata['data']['name']);
                    }

                    $this->redirect('front/index/index');
                } else {
                    $this->error('请求连接失败');
                }
            }

            $rawdata = '0e9652bfe142fb25e9893a4b1e4fc7b0f60af75122f3eb5b5e49a01acaa2d9228f0366c44dde29da80fae5537d45da4750400034e41af7cbb2fcc2ffb09ee744';
            //设置post数据
            $data = [];
            $data['appid'] = Db::name('webset')->where('name', 'appid')->column('value')[0];
            $data['appsecret'] = Db::name('webset')->where('name', 'appsecret')->column('value')[0];
            $data['content'] = $rawdata;
            $output = HttpService::http("https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/OpenApi/decode", [], $data, "POST");
            $userdata = json_decode($output, true);
            Debug::dump($accessToken);
            if($userdata['status'] == 1) {
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
                    session('user_'.$item, isset($userdata['data'][$item]) ? $userdata['data'][$item] : "");
                }
                //用户信息写入数据库
                (new \app\frontend\model\User())->insertStudent($userdata['data']['card_number'], $userdata['data']['name']);

                Debug::dump("444444444444444444444444444".$accessToken);
                $this->redirect('frontend/index/index',['access_token' => $accessToken]);
            } else {
                $this->error('请求连接失败');
            }
        } else{
            return 'app\common\Base: '.session('user_id')."  ".session('user_name')." ".$accessToken;
            //$this->redirect('frontend/index/index',['access_token' => $accessToken]);
        }*/
    }

    public function get_user_data()
    {
        Debug::dump(session('user_id'));
        if(!session('user_id')) {
            //获得加密的rawdata

            $rawdata = '0e9652bfe142fb25e9893a4b1e4fc7b0f60af75122f3eb5b5e49a01acaa2d9228f0366c44dde29da80fae5537d45da4750400034e41af7cbb2fcc2ffb09ee744';
            //设置post数据
            $data = [];
            $data['appid'] = Db::name('webset')->where('name', 'appid')->column('value')[0];
            $data['appsecret'] = Db::name('webset')->where('name', 'appsecret')->column('value')[0];
            $data['content'] = $rawdata;
            $output = HttpService::http("https://icampus.ss.pku.edu.cn/iaaa/index.php/Home/OpenApi/decode", [], $data, "POST");
            $userdata = json_decode($output, true);
            if($userdata['status'] == 1) {
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
                    session('user_'.$item, isset($userdata['data'][$item]) ? $userdata['data'][$item] : "");
                }
                //用户信息写入数据库
                (new \app\frontend\model\User())->insertStudent($userdata['data']['card_number'], $userdata['data']['name']);
            } else {
                $this->error('请求连接失败');
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