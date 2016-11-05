<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午1:32
 */

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class DataController extends Controller
{

//   news 39BFnXiz_zuHfo2Y8qvkjQqe6rqxsGfs1Uuc8bLqrmhskUWPJdZ4Rw3G9zJ9ou7z
    public $data = '{
                        "articles": [
                                {
                                    "thumb_media_id":"AV_QUvTpROCCUEDeqV5jBjCDn8ZT-Bpm4DIWSf4UjbhFsVgbjbEVFg47qF2FRn3I",
                                    "author":"BIAG",
                                    "title":"Happy Day",
                                    "content_source_url":"www.qq.com",
                                    "content":"content",
                                    "digest":"digest",
                                    "show_cover_pic":1
                                },
                                {
                                    "thumb_media_id":"AV_QUvTpROCCUEDeqV5jBjCDn8ZT-Bpm4DIWSf4UjbhFsVgbjbEVFg47qF2FRn3I",
                                    "author":"BIAG",
                                    "title":"Happy Day",
                                    "content_source_url":"www.qq.com",
                                    "content":"content",
                                    "digest":"digest",
                                    "show_cover_pic":0
                                }
                        ]
                    }';
    public $jsonmenu = '{
          "button":[
          {
                "name":"天气预报",
               "sub_button":[
                {
                   "type":"click",
                   "name":"北京天气",
                   "key":"menu_weather_beijing"
                },
                {
                   "type":"click",
                   "name":"上海天气",
                   "key":"menu_weather_shanghai"
                },
                {
                   "type":"click",
                   "name":"广州天气",
                   "key":"menu_weather_guangzhou"
                },
                {
                   "type":"click",
                   "name":"深圳天气",
                   "key":"menu_weather_shenzhen"
                },
                {
                    "type":"view",
                    "name":"本地天气",
                    "url":"http://m.hao123.com/a/tianqi"
                }]


           },
           {
               "name":"附加功能",
               "sub_button":[
                {
                   "type":"view",
                   "name":"模版样例",
                   "url":"http://203.195.235.76/jssdk/"
                },
                {
                   "type":"click",
                   "name":"公司简介",
                   "key":"menu_company_detail"
                },
                {
                   "type":"click",
                   "name":"趣味游戏",
                   "key":"menu_game_fun"
                },
                {
                    "type":"click",
                    "name":"发送消息",
                    "key":"menu_joke"
                }]


           }]
        }';
    public $news_allSend = '{
                               "filter":{
                                  "is_to_all":false,
                                  "tag_id":100
                               },
                               "mpnews":{
                                  "media_id":"OyFT9nh3nYs5ls9x9uxaffe8Cwr2crUSs0aKvvKLy67fGgSQF8z_O3T3xaQed05u"
                               },
                               "msgtype":"mpnews"
                            }';

    public $text_allSend = '{
                               "filter":{
                                  "is_to_all":false,
                                  "tag_id":100
                               },
                               "text":{
                                  "content":"这是群发消息的测试"
                               },
                               "msgtype":"text"
                            }';
    //"id":100
    public $tag = '{
                    "tag" : {
                        "name" : "test"//标签名
                    }
                   }';
    public $user_tags = '{
                          "openid_list" : [//粉丝列表
                            "ocoIvxLTumwc3gpi6SPvKWrzYlt0",
                          ],
                          "tagid" : 100
                        }';

    public $sendByOpenID = '{
                               "touser":[
                                "ocoIvxLTumwc3gpi6SPvKWrzYlt0",
                                "ocoIvxLTumwc3gpi6SPvKWrzYlt0"
                               ],
                                "msgtype": "text",
                                "text": { "content": "请您获知."}
                            }';
}