<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function get_access_token(){
    // 获取access_token
    $access_token = cache('access_token');
    if (!in_array("ip_list" ,json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/get_api_domain_ip?access_token='.$access_token),true))) {
        $access_token = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.config('appid').'&secret='.config('appsecret')),true)['access_token'];
        cache('access_token',$access_token,7200);
    }
    return $access_token;
}