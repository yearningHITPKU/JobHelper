<?php

namespace app\admin\controller;

use app\admin\common\Base;
use think\Log;
use think\Request;

//引用excel用到的库
use PHPExcel_IOFactory;
use PHPExcel;
class Intern extends Base
{
    public function index()
    {
        //1.从数据库中读取所有is_allowed字段为1的数据，并且分页，参数为每页的条数
        $internModel = new \app\admin\model\Intern();

        $interns = $internModel->where('is_allowed',1)->order('')->paginate(10);

        //2.将数据赋值给模板
        $this->view->assign('intern_list', $interns);
        $this->view->assign('intern_size', count($interns));
        //3.分页器对象传给前端，显示页码
        $page = $interns->render();
        $this->view->assign('page', $page);
        //4.渲染模板
        return $this->view->fetch('check_list');
    }

    public function deleted_list()
    {
        //1.从数据库中读取所有is_allowed字段为0的数据
        $internModel = new \app\admin\model\Intern();
        $interns = $internModel->where('is_allowed',0)->paginate(5);
        //2.将数据赋值给模板
        $this->view->assign('intern_list', $interns);
        $this->view->assign('intern_size', count($interns));
        //3.分页器对象传给前端，显示页码
        $page = $interns->render();
        $this->view->assign('page', $page);
        //4.渲染模板
        return $this->view->fetch('deleted_list');
    }

    public function save(Request $request)
    {
            if(session('user_id')!='admin'){     //是否管理员操作
                return '未登录或登录已超时，请重新登录再导出';
            }

            $id = $request->param()['id'];
            if($id==-1){     //下载全部
                $data = model('Intern')->order('time_publish desc')->select();
            }else{//下载选中
                $ids = explode('-',$id);
                $data = \app\admin\model\Intern::all($ids);
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
            ->setCellValue('A1','ID')
            ->setCellValue('B1','标题')
            ->setCellValue('C1','行业')
            ->setCellValue('D1','公司')
            ->setCellValue('E1','简章')
            ->setCellValue('F1','资质')
            ->setCellValue('G1','职位')
            ->setCellValue('H1','地点')
            ->setCellValue('I1','年级')
            ->setCellValue('J1','offer种类')
            ->setCellValue('K1','邮箱')
            ->setCellValue('L1','电话')
            ->setCellValue('M1','网址')
            ->setCellValue('N1','微信号')
            ->setCellValue('O1','HR')
            ->setCellValue('P1','HR邮箱')
            ->setCellValue('Q1','HR电话')
            ->setCellValue('R1','详情')
            ->setCellValue('S1','发布学号')
            ->setCellValue('T1','审核状态')
            ->setCellValue('U1','是否置顶')
            ->setCellValue('V1','发布时间')
            ->setCellValue('W1','截止日期')
            ->setCellValue('X1','码')
        ;
        $objPHPExcel->getActiveSheet()->setTitle('实习内推信息');      //设置sheet的名称

        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X');
        foreach ($data as $key=>$intern){
            $row = $key+2;
            for ($column = 0;$column<count($letter);$column++){
                //写出长整数时，为避免被科学计数法表示，在前面加空格，用.连接
                if($column ==0){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->id);
                }elseif ($column ==1){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->title);
                }elseif ($column ==2){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->industry);
                }elseif ($column ==3){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->corp_name);
                }elseif ($column ==4){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->corp_hire_info);
                }elseif ($column ==5){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->corp_qualification);
                }elseif ($column ==6){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->position);
                }elseif ($column ==7){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->location);
                }elseif ($column ==8){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->grade);
                }elseif ($column ==9){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->type);
                }elseif ($column ==10){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->email);
                }elseif ($column ==11){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", ' '.$intern->tel);
                }elseif ($column ==12){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->url);
                }elseif ($column ==13){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->weixin);
                }elseif ($column ==14){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->hr_name);
                }elseif ($column ==15){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->hr_email);
                }elseif ($column ==16){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", ' '.$intern->hr_tel);
                }elseif ($column ==17){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->detail);
                }elseif ($column ==18){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", ' '.$intern->owner_id);
                }elseif ($column ==19){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->is_allowed);
                }elseif ($column ==20){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->is_pinned);
                }elseif ($column ==21){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->time_publish);
                }elseif ($column ==22){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->time_deadline);
                }elseif ($column ==23){
                    $objPHPExcel->getActiveSheet()->setCellValue("$letter[$column]$row", $intern->code);
                }
            }

        }

        $filename = '1.xlsx';  //输出的excel文档名称
        ob_end_clean();     //清空缓冲区避免乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
        header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 将选定的一条实习内推信息添加到已删除列表
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());

            $intern = new \app\admin\model\Intern;
            // save方法第二个参数为更新条件
            $result = $intern->save([
                'is_allowed'  => '0',
            ],['id' => $data['id']]);

            // save方法第二个参数为更新条件
            $result = $intern->save(
                ['is_allowed'  => '0'],
                ['id' => $data['id']]
            );

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

    //置顶
    public function stick(Request $request)
    {
        if ($request->isAjax(true)) {
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());

            $intern = new \app\admin\model\Intern;

            // save方法第二个参数为更新条件
            $result = $intern->save(
                ['is_pinned' => '1'],
                ['id' => $data['id']]
            );

            //更新成功的提示信息
            $status = 1;
            $message = '置顶成功';
            //更新失败
            if (is_null($result)) {
                $status = 0;
                $message = '置顶失败';
            }
        }
        return ['status'=>$status, 'message'=>$message];
    }

    public function stick_cancel(Request $request){
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());

            $intern = new \app\admin\model\Intern;

            // save方法第二个参数为更新条件
            $result = $intern->save(
                ['is_pinned' => '0'],
                ['id' => $data['id']]
            );

            //更新成功的提示信息
            $status = 1;
            $message = '取消成功';
            //更新失败
            if(is_null($result)){
                $status = 0;
                $message = '取消失败';
            }
        }

            return ['status'=>$status, 'message'=>$message];
    }

    /**
     * 将所有选定实习内推信息添加到已删除列表
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateSelected(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());
            //填写日志
            Log::record($data);
            //解析ajax发送的数据
            $dejson = json_decode($data['json']);
            Log::record($dejson);
            //实例化模型
            $intern = new \app\admin\model\Intern;
            //构建用于更新数据库的条件数组
            $list = array();
            for($i = 0; $i < count($dejson); $i++){
                $list[$i] = ['id'=>$dejson[$i], 'is_allowed'=>0];
            }
            Log::record($list);
            //批量更新表中数据
            $result = $intern->saveAll($list);
            //更新成功的提示信息
            $status = 1;
            $message = '更新成功';
            //更新失败
            if(is_null($result)){
                $status = 0;
                $message = '更新失败';
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
            $intern = new \app\admin\model\Intern;
            //设置删除条件
            $list = array();
            for($i = 0; $i < count($dejson); $i++){
                $list[$i] = $dejson[$i];
            }
            Log::record($list);
            //批量删除表中数据
            $result = \app\admin\model\Intern::destroy($list);
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

    public function recoverSelected(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());
            //填写日志
            Log::record($data);
            //解析ajax发送的数据
            $dejson = json_decode($data['json']);
            Log::record($dejson);
            //实例化模型
            $intern = new \app\admin\model\Intern;
            //构建用于更新数据库的条件数组
            $list = array();
            for($i = 0; $i < count($dejson); $i++){
                $list[$i] = ['id'=>$dejson[$i], 'is_allowed'=>1];
            }
            Log::record($list);
            //批量更新表中数据
            $result = $intern->saveAll($list);
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


    /**
     * 恢复选定的一条实习内推信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function recover(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());

            $intern = new \app\admin\model\Intern;
            // save方法第二个参数为更新条件
            $result = $intern->save([
                'is_allowed'  => '1',
            ],['id' => $data['id']]);

            //更新成功的提示信息
            $status = 1;
            $message = '恢复成功';
            //更新失败
            if(is_null($result)){
                $status = 0;
                $message = '恢复失败';
            }
        }

        return ['status'=>$status, 'message'=>$message];
    }

    /**
     * 删除选定的一条实习内推信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete(Request $request)
    {
        if($request->isAjax(true)){
            //获取请求数据，自动过滤空值
            $data = array_filter($request->param());
            //设置删除条件
            $map = ['id' => $data['id']];
            //更新intern表中的数据
            $result = \app\admin\model\Intern::destroy($map);

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
}
