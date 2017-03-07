<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 2016/12/7
 * Time: 上午10:10
 */

namespace Home\Controller;

use Think\Controller;

class ShowController extends Controller
{
    public function show($openid = null)
    {
        if ($openid == null) {
            $openid = I('get.oi');
        }
//        $openid = 'ocoIvxLTumwc3gpi6SPvKWrzYlt0';
        $user = M('user');
        $condition['openid'] = $openid;
        $name = $user->field('stu_name')
            ->where($condition)
            ->select();
        $this->assign('stu_name', $name[0]['stu_name']);
        $this->display();
    }
}