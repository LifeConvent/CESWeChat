<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/20
 * Time: 下午3:15
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

define("TOKEN", "scce");

//traceHttp();

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
//        取得XML数据包信息
        $postStr = file_get_contents('php://input');
        //extract post data
        if (!empty($postStr)) {
            //启动安全防御
            libxml_disable_entity_loader(true);
            //解析XML数据包
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            catchEvent($postObj);
        }
    }

    public function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}


class AccessController extends Controller
{
    public function access()
    {
        $wechatObj = new wechatCallbackapiTest();
        if ($_GET['echostr']) {
            $wechatObj->valid(); //如果发来了echostr则进行验证
        } else {
            $wechatObj->responseMsg(); //如果没有echostr，则返回消息
        }
    }

    public function search($content = null)
    {
        $auto_res = M('auto_response');
        $condition['user_input'] = "$content";
        $result = $auto_res->where($condition)->find();
        if ($result) {
            return $result['sys_response'];
        } else {
            return '';
        }
    }

    public function searchSurvey($openid = null)
    {
        $survey_plan = M('survey_plan');
//        $openid = 'ocoIvxLTumwc3gpi6SPvKWrzYlt0';
        $condition['openid'] = "$openid";
        $condition['is_finish'] = '0';
        $result = $survey_plan->where($condition)->select();
        if ($result) {
            return $result;
        } else {
            return '';
        }
    }

    public function output($object)
    {
        $text = "请按照以下提示输入获取信息:\n1.  输入 \"课程评价\" 获取课程评价问卷\n";
        $auto = M('auto_response');
        $res = $auto->field('user_input')->select();
        for ($i = 0; $i < sizeof($res); $i++) {
            $text .= (($i + 2) . ".  输入 \"" . $res[$i]['user_input'] . "\" \n");
        }
//        dump($text);
        echo transmitText($object, $text);
    }

}


/**
 * @function 处理微发来的消息时间
 * @param $object
 * @return string
 */
function catchEvent($object)
{
    //对象为空时要向微信返回空字符串
    if (empty($object))
        echo '';
    $OpenID = $object->FromUserName;
    $userMana = new UserManagerController();
    $result = $userMana->searchUserByOpenID($OpenID);

    if (!$result) {
        $newsArray = array();
        $newsArray[] = array("Title" => "请绑定您的微信", "Description" => "您还未未绑定微信账号，绑定后使用更多功能", "PicUrl" => 'http://' . $_SERVER['HTTP_HOST'] . "/CES/Public/image/sample.jpg", "Url" => $_SERVER['HTTP_HOST'] . "/CES/index.php/Home/UserManager/addOpenInfo?id=" . $OpenID);
        echo transmitNews($object, $newsArray);
        exit();
    } else {

        switch ($object->MsgType) {
            case 'text': {
                $AC = new AccessController();
                $content = $object->Content;
                if ($content == '课程评价') {
                    $surveyArray = array();
                    $survey = $AC->searchSurvey($OpenID);
                    if ($survey != '') {
                        $surveyArray[] = array("Title" => "您有新的课程评价问卷待完成", "Description" => "您有新的课程评价问卷待完成", "PicUrl" => 'http://' . $_SERVER['HTTP_HOST'] . "/CES/Public/image/sample.jpg", "Url" => $_SERVER['HTTP_HOST'] . "/CESBack/index.php/Home/SurveyPublish/surPubBef?oi=" . $OpenID);
                        echo transmitNews($object, $surveyArray);
                        exit();
                    } else {
                        echo transmitText($object, '您已完成所有课程评价问卷');
                        exit();
                    }
                    break;
                }
                $result = $AC->search($content);
                if ($result != '') {
                    echo transmitText($object, $result);
                    exit();
                } else {
                    $AC->output($object);
                    exit();
                }
                break;
            }
            case 'event': {
                switch ($object->Event) {
                    case 'subscribe': {
                        $OpenID = $object->FromUserName;
                        $result = $userMana->searchUserByOpenID($object->FromUserName);
                        if (!$result) {
                            $newsArray = array();
                            $newsArray[] = array("Title" => "请绑定您的微信", "Description" => "您还未未绑定微信账号，绑定后使用更多功能", "PicUrl" => 'http://' . $_SERVER['HTTP_HOST'] . "/CES/Public/image/sample.jpg", "Url" => $_SERVER['HTTP_HOST'] . "/CES/index.php/Home/UserManager/addOpenInfo?id=" . $OpenID);
                            echo transmitNews($object, $newsArray);
                        } else {
                            echo transmitText($object, "已绑定");
                        }
                        exit();
                        break;
                    }
                    case 'CLICK': {
                        $key = $object->EventKey;
                        $menu = new MenuController();
                        $result = $menu->searchByMenuKey($key);
                        echo transmitText($object, $result);
                        exit();
                    }
                }
                break;
            }
            default:
                echo '';
                break;
        }

    }
}

/**
 * @function 回复图文消息
 * @param $object
 * @param $newsArray
 * @return string
 */
function transmitNews($object, $newsArray)
{
    if (!is_array($newsArray)) {
        return '';
    }
    $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                </item>";
    $item_str = "";
    foreach ($newsArray as $item) {
        $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
    }
    $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                        $item_str
                    </Articles>
               </xml>";

    $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
    return $result;
}

/**
 * @function 回复文字消息
 * @param $object
 * @param $contentStr
 * @return string
 */
function transmitText($object, $contentStr)
{
    $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
    return sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), 'text', $contentStr);
}

function traceHttp()
{
    logger("REMOTE_ADDR:" . $_SERVER["REMOTE_ADDR"] . ((strpos($_SERVER["REMOTE_ADDR"], "101.226")) ? "From WeiXin" : "Unkonow IP"));
    logger("QUERY_STRING:" . $_SERVER["QUERY_STRING"]);
}

function logger($content)
{
    file_put_contents("log.html", date('Y-m-d H:i:s  ') . $content . "<br>", FILE_APPEND);
}


