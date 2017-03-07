<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 2016/11/24
 * Time: 下午5:13
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class SetAutoResController extends Controller
{
    public function setRes(){
        $user_input = I('post.user_input');
        $sys_response = I('post.sys_response');
        $type = I('post.type');
        if($user_input==null||$sys_response==null){
            $result['status'] = 'failed';
            $result['message'] = '消息内容不能为空！';
            exit(json_encode($result));
        }else{
            $response = M('auto_response');
            $condition['user_input'] = $user_input;
            if($response->where($condition)->select()){
                $result['status'] = 'failed';
                $result['message'] = '该关键字回复已存在，请使用更新操作！';
                exit(json_encode($result));
            }
            $condition['sys_response'] = $sys_response;
            $addResult = $response->add($condition);
            if($addResult){
                $result['status'] = 'success';
            }else{
                $result['status'] = 'failed';
                $result['message'] = '插入失败！';
            }
        }
        exit(json_encode($result));
    }
}