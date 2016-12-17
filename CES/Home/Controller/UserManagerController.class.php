<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午2:17
 * function  设置用户标签分组接口 {:U('Home/UserManager/uploaderSetTag')} POST数据：标签名称 网址：http://HOST:PORT/CES/index.php/Home/UserManager/uploaderSetTag
 * function  为标签添加用户接口 {:U('Home/UserManager/setTagUser')} POST数据：用户学号数组、发送文本内容（支持HTML） 网址：http://HOST:PORT/CES/index.php/Home/UserManager/setTagUser
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class UserManagerController extends Controller
{
    /**
     * @function 向服务器发送创建标签申请
     * @param null $tag POST tag名称
     * @return bool true成功 false失败
     */
    public function uploaderSetTag($tag = null)
    {
        if ($tag == null) {
            $tag = I('post.tag');
//            $tag = I('get.tag');
        }
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=' . $access_token;
        if ($tag == null) {
            return false;
        }
        $up_tag = '{
                    "tag" : {
                        "name" : "' . $tag . '"
                    }
                   }';

        $result = $upMenu->https_request($url, $up_tag);
//        dump($result);
        $data = new \stdClass();
        $data = json_decode($result);
        $data = $data->tag;
        if ($data->id == null || $data->name == null) {
            return false;
        }
        //将获得的对应标签用户id name 存储用户OpenID至数据库
        $tag_info['tag_id'] = $data->id;
        $tag_info['tag_name'] = $data->name;
        $tag = M('user_tag');
        $result = $tag->add($tag_info);
        dump($result);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**    未测试接口
     * @function 通过标签ID标记用户
     * @param null $tagid 微信端分组标签id
     * @param null $userid 要被标记的用户数组，以此获取OpenID
     * @return bool true成功标记
     */
    public function setTagUser($tagid = null, $userid = null)
    {
        if ($tagid == null || $userid == null) {
            $tag = I('post.tag');
            $userid = I('post.userid');
        }
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=' . $access_token;
        if ($tagid == null || $userid == null) {
            return false;
        }

        $useridList = array();
        if (!is_array($userid)) {
            $useridList[] = $userid;
        } else {
            $useridList = $userid;
        }
        $openIDList = array();
        $groupSend = new GroupSendController();
        for ($i = 0; $i < sizeof($useridList); $i++) {
            $openIDList[] = $groupSend->searchUserByUserID($useridList[$i]);
//                dump($openIDList);
        }

        $user_tags1 = '{
                          "openid_list" : [
                            "';
        for ($i = 0; $i < sizeof($openIDList); $i++) {
            if ($i == sizeof($openIDList) - 1) {
                $user_tags1 .= $openIDList[$i];
            } else {
                $user_tags1 .= $openIDList[$i] . '","';
            }
        }
        $user_tags2 = '",
                          ],
                          "tagid" : ' . $tagid . '
                        }';
        $user_tags = $user_tags1 . $user_tags2;

        $result = $upMenu->https_request($url, $user_tags);
        $data = new \stdClass();
        $data = json_decode($result);
        if ($data->errmsg == 'ok')
            return true;
        else
            return false;
    }

    public function addOpenInfo()
    {
        $OpenID = I('get.id');
        $user = M('user');
        $condition['openid'] = $OpenID;
        $res = $user->field('stu_name')->where($condition)->select();
        if ($res) {
            $show = new ShowController();
            $show->assign('stu_name', $res[0]['stu_name']);
            $show->display('Show/show');
            return;
        }
        $this->assign('openid', $OpenID);
        $this->display();
    }

    public function bindUserInfo()
    {
        $OpenId = I('post.openid');
        $stuName = I('post.stu_name');
        $stuNum = I('post.stu_num');
        $stuPro = I('post.stu_pro');
        //能进入绑定界面的即认为为绑定的人员信息，直接进行绑定
        $res = $this->checkUserInfo($stuName, $stuNum, $stuPro);
        if ($res == '1') {
            if (!($this->searchUserByOpenID($OpenId))) {
                if (!$this->searchUserOpenIDByNum($stuNum)) {
                    $res = $this->updateUserInfo($OpenId, $stuNum);
                    if ($res) {
                        $result['status'] = 'success';
                        $result['hint'] = '绑定成功！';
                    } else {
                        $result['status'] = 'failed';
                        $result['hint'] = '绑定失败！';
                    }
                } else {
                    $result['status'] = 'failed';
                    $result['hint'] = '该用户已绑定微信号，无需重复绑定！';
                }
            } else {
                $result['status'] = 'failed';
                $result['hint'] = '该微信号已绑定用户，不能重复绑定！';
            }
        } else if ($res == '0') {
            $result['status'] = 'failed';
            $result['hint'] = '用户不存在！';
        } else {
            $result['status'] = 'failed';
            $result['hint'] = '用户信息不匹配！';
        }
        exit(json_encode($result));
    }

    public function checkUserInfo($stuName = null, $stuNum = null, $stuPro = null)
    {
//        $stuName='高彪';
//        $stuNum='2013558';
//        $stuPro='2';
        $user = M('user');//实例化user_info表模型对象
        $temp['stu_name'] = $stuName;
        $temp['stu_num'] = $stuNum;
        $temp['stu_pro'] = $stuPro;
        $condition['stu_num'] = "$stuNum";
        $result = $user->where($condition)->select();//对象查找
        if (!$result) {
//            echo '0';
            return '0';
        } else if ($result[0]['stu_name'] == $temp['stu_name'] && $temp['stu_pro'] == $result[0]['stu_pro']) {
//            echo '1';
            return '1';
        } else {
//            echo '2';
            return '2';
        }
    }

    public function updateUserInfo($OpenId, $stuNum)
    {
        $user = M('user');//实例化user_info表模型对象
        if ($OpenId == null || $stuNum == null) {
            return false;
        }
        $temp['openid'] = "$OpenId";
        $temp['is_match'] = '是';
        $condition['stu_num'] = "$stuNum";
        $result = $user->where($condition)->save($temp);//对象插入
        if ($result) {
            $sur_plan = M('survey_plan');
            $open['openid'] = "$OpenId";
            $num['stu_num'] = "$stuNum";
            $sur_plan->where($num)->save($open);
            return true;
        } else {
            return false;
        }
    }

    public function searchUserByOpenID($OpenID = null)
    {
        $user = M('user');//实例化user_info表模型对象
        $temp["openid"] = "$OpenID";

        $list = $user->where($temp)->find();//对象查询

        if ($list) {
//            echo '0';
            return true;
        } else {
            return false;
        }
    }

    public function searchUserOpenIDByNum($stuNum = null)
    {
        $user = M('user');//实例化user_info表模型对象
        $condition['stu_num'] = "$stuNum";
        $res = $user->field('openid')->where($condition)->find();//对象查询
        if ($res['openid'] != '' && $res['openid'] != 'NULL' && $res['openid'] != null) {
            return true;
        }
        return false;
    }


    public function write($userid = null, $content = null)
    {
        $myfile = fopen("Public/file/" . $userid . ".txt", "wb") or die("Unable to open file!");
        file_put_contents("Public/file/" . $userid . ".txt", $content);
        fclose($myfile);
    }

    public function read($userid = null)
    {
        $userid = 'test';
        $result = file_get_contents("Public/file/" . $userid . ".txt");
        return $result;
    }

    public function searchUserNameByOpenID($OpenID = null)
    {
        $user = M('user');//实例化user_info表模型对象
        $temp["openid"] = "$OpenID";

        $list = $user->where($temp)->find();//对象查询

        if ($list) {
//            echo '0';
            return $list['stu_name'];
        } else {
            return '';
        }
    }
}