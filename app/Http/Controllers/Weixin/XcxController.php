<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Model\WxUserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class XcxController extends Controller
{

    /**
     * 小程序登录
     */
    public function login(Request $request)
    {
        //接收code
        $code = $request->get('code');

        //使用code
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.env('WX_XCX_APPID').'&secret='.env('WX_XCX_SECRET').'&js_code='.$code.'&grant_type=authorization_code';

        $data = json_decode( file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';

        //自定义登录状态
        if(isset($data['errcode']))     //有错误
        {
            $response = [
                'errno' => 50001,
                'msg'   => '登录失败',
            ];

        }else{              //成功

            $openid = $data['openid'];          //用户OpenID
            //判断新用户 老用户
            $u = WxUserModel::where(['openid'=>$openid])->first();
            if($u)
            {
                // TODO 老用户
                //echo "老用户";
            }else{
                //echo "新用户入库";
                $u_info = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'type'      => 3        //小程序
                ];

                WxUserModel::insertGetId($u_info);
            }


            $token = sha1($data['openid'] . $data['session_key'].mt_rand(0,999999));
            //保存token
            $redis_key = 'xcx_token:'.$token;
            Redis::set($redis_key,time());
            // 设置过期时间
            Redis::expire($redis_key,7200);

            $response = [
                'errno' => 0,
                'msg'   => 'ok',
                'data'  => [
                    'token' => $token
                ]
            ];
        }

        return $response;
    }
}
