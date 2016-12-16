<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午1:50
 * function  指定用户发送文本信息借口 {:U('Home/GroupSend/sendTextArray')} POST数据：用户学号数组、发送文本内容（支持HTML） 网址：http://HOST:PORT/CES/index.php/Home/GroupSend/sendTextArray
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class GroupSendController extends Controller
{

    /**
     * @function 群发消息给用户，可通过标签发送，发送过程不涉及OpenID
     * @param bool|true $toAll 接口调用不需要传入
     * @param null $tagID 用户标签，不针对标签群发时不用传入
     * @param null $type 群发消息类型 text、mpnews 其它暂未开放
     * @param null $content 文字消息内容
     * @param null $media_id 图文消息媒体id号，需提前获得
     * @return bool 成功发送时返回trueF
     */
    public function allSendNews($toAll = true, $tagID = null, $type = null, $content = null, $media_id = null, $openid = null)
    {
        if (I('post.ispost') == '1') {
            $toAll = I('post.toall');
            $tagID = I('post.tagid');
            $type = I('post.type');
            $content = I('post.content');
            $media_id = I('post.mediaid');
            $openid = I('post.openid');
        }
        if ($openid != null) {
            $toAll = false;
        }
        if ($toAll) {
            if ($type == 'text') {
                if ($content == null) {
                    $result['status'] = 'failed';
                    $result['message'] = '发送内容不能为空';
                    exit(json_encode($result));
                }
                $allSend = '{
                               "filter":{
                                  "is_to_all":true
                               },
                               "text":{
                                  "content":"' . $content . '"
                               },
                               "msgtype":"type"
                            }';
            } else if ($type == 'mpnews') {
                if ($media_id == null) {
                    $result['status'] = 'failed';
                    $result['message'] = '素材获取失败';
                    exit(json_encode($result));
                }
                $allSend = '{
                               "filter":{
                                  "is_to_all":true
                               },
                               "mpnews":{
                                  "media_id":"' . $media_id . '"
                                },
                               "msgtype":"mpnews"
                            }';
            } else {
                return false;
            }
        } else {
            if ($type == 'text') {
                if ($content == null) {
                    $result['status'] = 'failed';
                    $result['message'] = '发送内容不能为空';
                    exit(json_encode($result));
                }
                if ($tagID == null && $openid != null) {
                    $openidList = array();
                    if (!is_array($openid)) {
                        $useridList[] = $openid;
                    } else {
                        $useridList = $openid;
                    }
                    $sendByOpenID1 = '{"touser":["';
                    $sendByOpenID2 = '"],"msgtype": "text","text": { "content": "' . $content . '"}}';
                    if (sizeof($useridList) == 1) {
                        $sendByOpenID1 .= $openid[0] . '","' . $openid[0];
                    } else {
                        for ($i = 0; $i < sizeof($useridList); $i++) {
                            if ($i == sizeof($useridList) - 1) {
                                $sendByOpenID1 .= $useridList[$i];
                            } else {
                                $sendByOpenID1 .= $useridList[$i] . '","';
                            }
                        }
                    }
                    $sendByOpenID = $sendByOpenID1 . $sendByOpenID2;
                    $send = new MenuController();
                    $access_token = $send->getAccessToken();
                    $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $access_token;
                    $send_result = $send->https_request($url, $sendByOpenID);
                    $data = new \stdClass();
                    $data = json_decode($send_result);
                    if ($data->errcode == 0) {//0
//                $data->errmsg;//send job submission success
//                $data->msg_id;
                        $result['status'] = 'success';
                    } else {
                        $result['status'] = 'failed';
                        $result['message'] = $data->errcode;
                    }
                    exit(json_encode($result));
                } else {
                    $allSend = '{
                               "filter":{
                                  "is_to_all":false,
                                  "tag_id":' . $tagID . '
                               },
                               "text":{
                                  "content":"' . $content . '"
                               },
                               "msgtype":"text"
                            }';
                }
            } else if ($type == 'mpnews') {
                if ($media_id == null) {
                    $result['status'] = 'failed';
                    $result['message'] = '素材获取失败';
                    exit(json_encode($result));
                }
                $allSend = '{
                               "filter":{
                                  "is_to_all":false,
                                  "tag_id":' . $tagID . '
                               },
                               "mpnews":{
                                  "media_id":"' . $media_id . '"
                                },
                               "msgtype":"mpnews"
                            }';
            } else {
                $result['status'] = 'failed';
                $result['message'] = '暂不支持除文本外的其他类型';
                exit(json_encode($result));
            }
        }
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
        $up_result = $upMenu->https_request($url, $allSend);
        $data = new \stdClass();
        $data = json_decode($up_result);
//        $data->errcode;//0
//        $data->errmsg;//send job submission success
//        $data->msg_id;
//        $data->msg_data_id;
        if ($data->errcode == 0) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'failed';
            //增加错误信息匹配函数，直接返回错误信息类型
            $result['message'] = $data->errcode;
        }
        exit(json_encode($result));
    }

    /**
     * 测试失败，没有效果
     * @function 指定用户预览群发文本消息入口
     */
    public function sendTextPreview()
    {
        $openid = I('post.openid');
        $content = I('post.content');

        if ($content == null || $openid == null) {
            $result['status'] = 'failed';
            $result['message'] = '发送内容不能为空';
            exit(json_encode($result));
        }

        $openidList = array();
        if (!is_array($openid)) {
            $useridList[] = $openid;
        } else {
            $useridList = $openid;
        }
        $sendByOpenID1 = '{"touser":["';
        $sendByOpenID2 = '"],"msgtype": "text","text": { "content": "' . $content . '"}}';
        $sendByOpenID1 .= $openid[0];
        $sendByOpenID = $sendByOpenID1 . $sendByOpenID2;

        $send = new MenuController();
        $access_token = $send->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $access_token;

        $send_result = $send->https_request($url, $sendByOpenID);
        $data = new \stdClass();
        $data = json_decode($send_result);
        if ($data->errcode == 0) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'success';
            $result['message'] = $data->errcode;
        }
        exit(json_encode($result));
    }

    /**
     * @function 指定用户群发文本消息入口
     */
    public function sendTextArray()
    {
        $userid = I('post.initial');
        $content = I('post.content');

        $useridList = array();
        if (!is_array($userid)) {
            $useridList[] = $userid;
        } else {
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

    /**
     * 入口为 senfTextArray
     * @param null $userid -数组多个用户特征
     * @param null $content 向用户发送的内容
     * @return bool true 成功发送
     * @function 通过OpenID向多个用户发送信息
     * 成功返回的格式 {"errcode":0,"errmsg":"send job submission success","msg_id":3147483650}
     */
    public function sendByOpenId($userid = null, $content = null)
    {
        if ($userid == null || $content == null) {
            //第二种方式，从url获取
            $userid = I('post.userid');
            $content = I('post.content');
        }

//        dump($userid);

        $useridList = array();
        for ($i = 0; $i < sizeof($userid); $i++) {
            if ($i == 0) {
                $useridList[] = $useridList[$i];
            }
            $useridList[] = $this->searchUserByUserID($userid[$i]);
//                dump($useridList);
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

            if ($data->errcode == 0) {//0
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
        $user = M('user');//实例化user表模型对象
        $temp['openid'] = "$OpenID";//$OpenID;
        $user_info = $user->where($temp)->select();//对象查询
        if (!$user_info) {
            return '';
        } else {
            return '1';
        }
    }

    public function searchUserByUserID($userid)
    {
        $user = M('user');//实例化user_info表模型对象
//        dump($userid);
        $condition['stu_num'] = "$userid";
        $openid = $user->field('openid')->where($condition)->find();//对象查询
//        dump($openid);
        if (!$openid) {
            return '';
        } else {
            return $openid['openid'];
        }
    }

    public function sendTest()
    {
        $OpenID = I('get.id');
        $this->assign('openid', $OpenID);
        $this->display();
    }
}