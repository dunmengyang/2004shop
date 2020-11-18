<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function __construct()
    {
        app('debugbar')->disable();     //关闭调试
    }

    public function userInfo()
    {
        echo __METHOD__;
    }

    public function test()
    {

        $goods_info = [
            'goods_id'  => 13345,
            'goods_name'    => "IPHONE",
            'price'     => 12.34
        ];

        echo json_encode($goods_info);

    }

    /**
     * 商品列表
     */
    public function goodsList()
    {
        $g = GoodsModel::select('goods_id','goods_name','shop_price','add_time')->limit(10)->get()->toArray();

        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list'  => $g
            ]
        ];

        return $response;

    }
}
