<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午2:17
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class UserManagerController extends Controller
{
    public function uploaderSetTag()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=' . $access_token;
        $dataClass = new DataController();
        $result = $upMenu->https_request($url, $dataClass->tag);
        $data = new \stdClass();
        $data = json_decode($result);
        $data = $data->tag;
        //将获得的对应标签用户id name 存储用户OpenID至数据库  未处理错误时的情况
        $data->id;
        $data->name;
    }

    public function setTagUser()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=' . $access_token;
        $dataClass = new DataController();
        $result = $upMenu->https_request($url, $dataClass->user_tags);
        $data = new \stdClass();
        $data = json_decode($result);
        if ($data->errmsg == 'ok')
            return true;
        else
            return false;
    }

    public function addOpenInfo()
    {
        $OpenID = $_GET['id'];
        $this->assign('openid', $OpenID);
        $this->display();
    }

    public function bindUserInfo()
    {
        $OpenId = $_POST['openid'];
        $stuName = $_POST['stu_name'];
        $stuNum = $_POST['stu_num'];
        $stuPro = $_POST['stu_pro'];
        //能进入绑定界面的即认为为绑定的人员信息，直接进行绑定
        $res = $this->checkUserInfo($stuName,$stuNum,$stuPro);
        if($res=='1'){
            $res = $this->updateUserInfo($OpenId,$stuNum);
            if ($res) {
                $result['status'] = 'success';
                $result['hint'] = '绑定成功！';
            } else {
                $result['status'] = 'failed';
                $result['hint'] = '绑定失败！';
            }
        }else if($res=='0'){
            $result['status'] = 'failed';
            $result['hint'] = '用户不存在！';
        }else{
            $result['status'] = 'failed';
            $result['hint'] = '用户信息不匹配！';
        }
        exit(json_encode($result));
    }

    public function checkUserInfo($stuName=null,$stuNum=null,$stuPro=null)
    {
//        $stuName='高彪';
//        $stuNum='2013558';
//        $stuPro='2';
        $user = M('user');//实例化user_info表模型对象
        $temp['stu_name'] = $stuName;
        $temp['stu_num'] = $stuNum;
        $temp['stu_pro'] = $stuPro;
        $condition['stu_num'] = $stuNum;
        $result = $user->where($condition)->select();//对象查找
        if(!$result){
//            echo '0';
          return '0';
        } else if ($result[0]['stu_name']==$temp['stu_name']&&$temp['stu_pro']==$result[0]['stu_pro']) {
//            echo '1';
            return '1';
        } else {
//            echo '2';
            return '2';
        }
    }

    public function updateUserInfo($OpenId,$stuNum)
    {
        $user = M('user');//实例化user_info表模型对象
        if($OpenId==null||$stuNum==null){
            return false;
        }
        $temp['openid'] = "$OpenId";
        $condition['stu_num'] = "$stuNum";
        $result = $user->where($condition)->save($temp);//对象插入
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function searchUserByOpenID($OpenID=null)
    {
        $user = M('user');//实例化user_info表模型对象
        $temp["openid"] = $OpenID;

        $list = $user->where($temp)->find();//对象查询

        if ($list) {
//            echo '0';
            return true;
        } else {
            return false;
        }
    }
}