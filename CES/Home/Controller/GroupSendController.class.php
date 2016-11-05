<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午1:50
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class GroupSendController extends Controller
{
    /**
     * @function 用户群发消息接口 无接口权限，测试完成
     */
    public function allSendNews()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
        $dataClass = new DataController();
        $result = $upMenu->https_request($url, $dataClass->news_allSend);
        $data = new \stdClass();
        dump($result);

        $data = json_decode($result);
        $data->errcode;//0
        $data->errmsg;//send job submission success
        $data->msg_id;
        $data->msg_data_id;
    }

    /**
     * @function 群发文本信息，可封装为一类函数
     */
    public function allSendText()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
        $dataClass = new DataController();
        $result = $upMenu->https_request($url, $dataClass->text_allSend);
        $data = new \stdClass();
        dump($result);

        $data = json_decode($result);
        //{"errcode":0,"errmsg":"send job submission success","msg_id":1000000001}
        $data->errcode;//0
        $data->errmsg;//send job submission success
        $data->msg_id;
        $data->msg_data_id;
    }


    /**
     * @param null $OpenID -数组多个用户
     * @param null $content 向用户发送的内容
     * @return bool true 成功发送
     * @function 通过OpenID向多个用户发送信息
     * 成功返回的格式 {"errcode":0,"errmsg":"send job submission success","msg_id":3147483650}
     */
    public function sendByOpenId($userid = null, $content = null)
    {
        if ($userid == null || $content == null) {
            //第二种方式，从url获取
            $userid = $_POST['userid'];
            $content = $_POST['content'];
        }
        $useridList = array();

        for ($i = 0; $i < sizeof($userid); $i++) {
            if ($i == 0) {
                $useridList[] = $useridList[$i];
            }
            $useridList[] = $this->searchUserByUserID($userid[$i]);
//            dump($useridList);
        }

        $sendByOpenID1 = '{"touser":["';
        $sendByOpenID2 = '"],"msgtype": "text","text": { "content": "' . $content . '"}}';
        for ($i = 0; $i < sizeof($useridList); $i++) {
            if ($i == sizeof($useridList) - 1) {
                $sendByOpenID1 .= $useridList[$i];
            } else {
                $sendByOpenID1 .= $useridList[$i] . '","';
            }
        }
        $sendByOpenID = $sendByOpenID1 . $sendByOpenID2;
//        dump($sendByOpenID);

        if (empty($content)) {
            return false;
        } else {
            $upMenu = new MenuController();
            $access_token = $upMenu->getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $access_token;
            $result = $upMenu->https_request($url, $sendByOpenID);
            $data = new \stdClass();
//            dump($result);
            $data = json_decode($result);

            if ($data->errcode === 0) {//0
//                $data->errmsg;//send job submission success
//                $data->msg_id;
                return true;
            } else {
                return false;
            }
        }
    }


    /**
     * @param $OpenID
     * @return string 查询userid的结果，不存在时返回为空
     */
    public function searchUserByOpenID($OpenID = null)
    {
        $user = M('user');//实例化user_info表模型对象
        $temp['openid'] = $OpenID;//$OpenID;
        $user_info = $user->where($temp)->select();//对象查询
        if (empty($user_info[0])) {
            return '';
        } else {
            return $user_info[0]['openid'];
        }
    }

    public function sendTest()
    {
        $OpenID = $_GET['id'];
        $this->assign('openid', $OpenID);
        $this->display();
    }

    public function sendText()
    {
        $userid = $_POST['initial'];
        $content = $_POST['content'];
        $OpenID = $this->searchUserByUserID($userid);

        if ($OpenID == '') {
            $result['status'] = 'failed';
            $result['hint'] = '发送失败,未获取到用户' . $userid . '------' . $content . '-------' . $OpenID;
            exit(json_encode($result));
        }

        $res = $this->sendByOpenId($OpenID, $content);
        if ($res) {
            $result['status'] = 'success';
            $result['hint'] = '发送成功！';
        } else {
            $result['status'] = 'failed';
            $result['hint'] = '发送失败';
        }
        exit(json_encode($result));
    }

    public function sendTextArray()
    {
        $userid = $_POST['initial'];
        $content = $_POST['content'];

        $useridList = array();
        if (!is_array($userid)) {
            $useridList[] = $userid;
        }else{
            $useridList = $userid;
        }

        if (empty($userid) || empty($content)) {
            $result['status'] = 'failed';
            $result['hint'] = '发送失败,未获取到用户';
            exit(json_encode($result));
        }

        $res = $this->sendByOpenId($useridList, $content);

        if ($res) {
            $result['status'] = 'success';
            $result['hint'] = '发送成功！';
        } else {
            $result['status'] = 'failed';
            $result['hint'] = '发送失败!';
        }
        exit(json_encode($result));
    }

    public function searchUserByUserID($userid)
    {
        $user = M('user');//实例化user_info表模型对象
        $condition['userid'] = $userid;
        $openid = $user->field('openid')->where($condition)->find();//对象查询
//        dump($openid);
        if (empty($openid['openid'])) {
            return '';
        } else {
            return $openid['openid'];
        }
    }
}