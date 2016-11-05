<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/25
 * Time: 下午2:05
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;


class MenuController extends Controller
{
    public $appid = "wx2c307a1875247a02";
    public $appsecret = "25cd61683a5b7282524f976cee2b0da9";
    public $appidTest = 'wx9753e9fbf21cbc59';
    public $appsecretTest = 'dcb93846b0a57b0c88ba1837613ed04c';
    public $url = '';

    public function getAccessToken()
    {
        $upMenu = new MenuController();
        $upMenu->url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$upMenu->appidTest&secret=$upMenu->appsecretTest";
        $output = $this->https_request($upMenu->url);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo["access_token"];
    }

    /**
     * @function 更新微信菜单
     * @return bool true为成功上传
     */
    public function upMenuList($menu = null)
    {
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
        if (empty($menu)) {
            $dataClass = new DataController();
            $result = $this->https_request($url, $dataClass->jsonmenu);
        } else
            $result = $this->https_request($url, $menu);
        //对返回结果的处理

        $data = new \stdClass();
        $data = json_decode($result);

        var_dump($result);

        if ($data->errmsg == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        if (class_exists('/CURLFile')) {//php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}


