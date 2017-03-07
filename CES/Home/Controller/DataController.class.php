<?php
/**
 * Created by PhpStorm.
 * User: lawrance
 * Date: 16/10/26
 * Time: 下午1:32
 */

namespace Home\Controller;

use Org\Util\Date;
use Think\Controller;
use Think\Model;

class DataController extends Controller
{
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


    /**
     * 后台系统隔天自动调用添加更新前一天的数据统计
     **/
    public function underUpdate()
    {
        $menu = new MenuController();
        $access_token = $menu->getAccessToken();

//        https://api.weixin.qq.com/datacube/getusersummary?access_token=ACCESS_TOKEN
//        {
//            "list":[
//                {
//                    "ref_date":"2016-12-17",
//                    "user_source":0,
//                    "new_user":0,
//                    "cancel_user":0
//                },
//                {
//                    "ref_date":"2016-12-17",
//                    "user_source":30,
//                    "new_user":1,
//                    "cancel_user":0
//                }
//            ]
//        }
        $url = "https://api.weixin.qq.com/datacube/getusersummary?access_token=" . $access_token;
        //日期最好调用一天的
        $time = time() - 60 * 60 * 24;
        $t_e = date("Y-m-d", $time);
        $time = time() - 60 * 60 * 24 * 2;
        $t_r = date("Y-m-d", $time);
        $data = '{"begin_date": "' . $t_r . '","end_date": "' . $t_e . '"}';
        echo $data;
        $result = $menu->https_request($url, $data);
//        $result = ' {
//            "list":[
//                {
//                    "ref_date":"2016-12-07",
//                    "user_source":0,
//                    "new_user":0,
//                    "cancel_user":0
//                },
//                {
//                    "ref_date":"2016-12-07",
//                    "user_source":30,
//                    "new_user":1,
//                    "cancel_user":0
//                }
//            ]
//        }';
//        dump($result);
        //对返回结果的处理

        echo json_decode($result);
        $data = new \stdClass();
        $data = json_decode($result);
        $dataList = $data->list;


        //时间
        $new_time = $dataList[0]->ref_date;
        //新增人数
        $new_user = $dataList[0]->new_user;


        if ($data->errcode!=null) {
            $temp['status_new'] = 'failed';
            $temp['message_new'] = $data->errcode;
        } else {
            $temp['status_new'] = 'success';
        }

//        https://api.weixin.qq.com/datacube/getusercumulate?access_token=ACCESS_TOKEN
//        {
//            "list": [
//                {
//                    "ref_date": "2014-12-07",
//                    "cumulate_user": 1217056
//                }, {
//        }]}
        $url = "https://api.weixin.qq.com/datacube/getusercumulate?access_token=" . $access_token;

        $result = $menu->https_request($url, $data);
//        $result = '{
//            "list": [
//                {
//                    "ref_date": "2014-12-07",
//                    "cumulate_user": 1217056
//                }, {
//        }]}';
//
//        dump($result);

        $data = new \stdClass();
        $data = json_decode($result);
        $dataList = $data->list;

        //时间
        $all_time = $dataList[0]->ref_date;
        //当前总数
        $all_num = $dataList[0]->cumulate_user;

        $condition['time'] = $new_time;
        $condition['new'] = $new_user;
        $condition['all_user'] = $all_num;

        $condition['survey'] = $this->getTotalSurvey();
        $condition['is_match'] = $this->getTotalUser();

        if ($data->errcode!=null) {
            $temp['status_all'] = 'failed';
            $temp['message_all'] = $data->errcode;
        } else {
            $temp['status_all'] = 'success';
            $home_count = M('home_count');
            $con['time'] = "$new_time";
            $res_b = $home_count->where($con)->select();
            dump($res_b);
            if (!$res_b) {
                dump($condition);
                $res = $home_count->add($condition);
                if ($res) {
                    $temp['insert'] = 'success';
                } else {
                    $temp['insert'] = 'failed';
                }
            } else {
                $temp['insert'] = 'failed';
            }
        }
        exit(json_encode($temp));
    }

    public function getTotalUser()
    {
        $user = M();
        $result = $user->where('openid!=\'NULL\'')
            ->table('tb_user')
            ->field('count(*)')
            ->query('SELECT %FIELD% AS total FROM %TABLE% %WHERE%', true);
        if ($result) {
            return $result[0]['total'];
        } else {
            return 0;
        }
    }

    public function getTotalSurvey()
    {
        $user = M();
        $result = $user->table('tb_survey_plan')
            ->field('count(*)')
            ->query('SELECT %FIELD% AS total FROM %TABLE%', true);
        if ($result) {
            return $result[0]['total'];
        } else {
            return 0;
        }
    }
}