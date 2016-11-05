<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/25
 * Time: 下午1:40
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class UploaderController extends Controller
{
    /**
     * @function 封面图长方形
     */
    public function upLoaderImage()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        //文件类型问题(获取文件类型后缀添加在url中)
        $file_type = $_GET['type'];
        $filename = $_GET['file'] . '.' . $file_type;
        $type = 'image';
        //文件地址
        $filedata = $this->getAbsolutePath($filename);
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=' . $type;
        $result = $upMenu->https_request($url, $filedata);


        //对结果的处理 type  media_id   created_at 与数据库中上传的对应的文件名及文件信息相匹配 封装函数?
        $data = new \stdClass();
        $data = json_decode($result);
        echo $data->type . '--------------' . $data->media_id . '------' . $data->created_at;
    }

    /**
     * @function 缩略图正方形
     */
    public function upLoaderThumbImage()
    {
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        //文件类型问题(获取文件类型后缀添加在url中)
        $file_type = $_GET['type'];
        $filename = $_GET['file'] . '.' . $file_type;
        $type = 'thumb';
        //文件地址
        $filedata = $this->getAbsolutePath($filename);
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=' . $type;
        $result = $upMenu->https_request($url, $filedata);


        //对结果的处理 type  media_id   created_at 与数据库中上传的对应的文件名及文件信息相匹配 封装函数?
        $data = new \stdClass();
        $data = json_decode($result);
        echo $data->type . '--------------' . $data->media_id . '------' . $data->created_at;
    }


    /**
     * @function 给定文件名获取绝对路径封装数组
     * @param $filename
     * @return array
     */
    public function getAbsolutePath($filename)
    {
        //路径设置
        $filepath = dirname(dirname(dirname(dirname(__FILE__)))) . '/Public/image/' . $filename;
        //关键是判断curlfile,官网推荐php5.5或更高的版本使用curlfile来实例文件
        if (class_exists('\CURLFile')) {
            $filedata = array(
                'fieldname' => new \CURLFile (realpath($filepath))
            );
        } else {
            $filedata = array(
                'fieldname' => '@' . realpath($filepath)
            );
        }
        return $filedata;
    }

    /**
     * @function 上传图文消息
     */
    public function upLoaderNews(){
        $upMenu = new MenuController();
        $access_token = $upMenu->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token='.$access_token;
        $dataClass = new DataController();
        $result = $upMenu->https_request($url, $dataClass->data);
        $data = new \stdClass();


        // type media_id created_at
        dump($result);
        $data = json_decode($result);
        $data->media_id;
    }
}
