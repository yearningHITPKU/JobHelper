<?php

namespace app\admin\controller;

use app\admin\common\Base;
use think\Db;
use think\Log;
use think\Request;
//引用excel用到的库
use PHPExcel_IOFactory;
use PHPExcel;

class User extends Base
{
    protected $db;
    public function _initialize()
    {
        parent::_initialize();
        $this->db = new \app\admin\model\User();
    }

    public function user_list()
    {
        $userModel = new \app\admin\model\User();
        $userList = Db::name('user')
            ->paginate(30);

        //分页器对象传给前端，显示页码
        $page = $userList->render();

        $this->view->assign('page', $page);
        $this->view->assign('userNum', $userList->count());
        $this->view->assign('user_list', $userList);

        return $this->view->fetch('user/userlist');
    }

    public function deleteOneUser(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());
            //设置删除条件
            $map = ['id' => $data['id']];
            //更新intern表中的数据
            $result = \app\admin\model\User::destroy($map);

            //更新成功的提示信息
            $status = 1;
            $message = '删除成功';
            //更新失败
            if(is_null($result)){
                $status = 0;
                $message = '删除失败';
            }
        }

        return ['status'=>$status, 'message'=>$message];
    }

    public function deleteSelected(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());
            //填写日志
            Log::record('-------------------------deleteSelected-------------------------');
            Log::record($data);
            //解析ajax发送的数据
            $dejson = json_decode($data['json']);
            Log::record($dejson);
            //实例化模型
            $intern = new \app\admin\model\User();
            //设置删除条件
            $list = array();
            for($i = 0; $i < count($dejson); $i++){
                $list[$i] = $dejson[$i];
            }
            Log::record($list);
            //批量删除表中数据
            $result = \app\admin\model\User::destroy($list);
            //更新成功的提示信息
            $status = 1;
            $message = '删除成功';
            //更新失败
            if(is_null($result)){
                $status = 0;
                $message = '删除失败';
            }
        }

        return ['status'=>$status, 'message'=>$message];

    }

    public function save(Request $request)
    {
        if(session('user_id')!='admin'){     //是否管理员操作
            return '未登录或登录已超时，请重新登录再导出';
        }

        $id = $request->param()['id'];
        if($id==-1){     //下载全部
            $data = model('User')->order('id asc')->select();
        }else{//下载选中
            $ids = explode('-',$id);
            $data = \app\admin\model\User::all($ids);
        }
        /*
         * 下载PHPExcel，将classes文件放进根目录的vendor目录。
         * 然后引入PHPExcel所需文件
        */
        vendor("PHPExcel.PHPExcel.Writer.IWriter");
        vendor("PHPExcel.PHPExcel.Writer.Abstract");
        vendor("PHPExcel.PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.PHPExcel.IOFactory");


        /*
         * 建立excel文件，新建工作表；
         */
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','学号')
            ->setCellValue('B1','姓名')
        ;
        $objPHPExcel->getActiveSheet()->setTitle('实习内推信息');      //设置sheet的名称

        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X');
        foreach ($data as $key=>$user){
            $row = $key+2;
            for ($column = 0;$column<count($letter);$column++){
                //写出长整数时，为避免被科学计数法表示，在前面加空格，用.连接
                if($column ==0){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $user->id);
                }elseif ($column ==1){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $user->name);
                }
            }
        }

        $filename = 'user.xlsx';  //输出的excel文档名称
        ob_end_clean();     //清空缓冲区避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
        header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

}
