<?php
// +----------------------------------------------------------------------
// | 滑块验证码控制器
// +----------------------------------------------------------------------
// | 开发者：杰哥网络科技
// | QQ: 2711793818
// +----------------------------------------------------------------------

namespace app\common\controller;

use extend\SliderCaptcha;
use think\response\Json;

/**
 * 滑块验证码控制器
 * 供后台管理和前端用户共用
 */
class CaptchaController
{
    /**
     * 获取验证码
     * @return Json
     */
    public function get(): Json
    {
        $captcha = new SliderCaptcha();
        $data = $captcha->get();
        
        return json([
            'code' => 1,
            'msg' => '获取成功',
            'data' => $data
        ]);
    }

    /**
     * 验证滑块
     * @return Json
     */
    public function check(): Json
    {
        $key = input('key', '');
        $x = input('x', 0);
        
        if (empty($key)) {
            return json([
                'code' => 0,
                'msg' => '验证码key不能为空'
            ]);
        }
        
        $captcha = new SliderCaptcha();
        $result = $captcha->check($key, (int)$x);
        
        if ($result) {
            return json([
                'code' => 1,
                'msg' => '验证成功',
                'data' => ['verified' => true]
            ]);
        }
        
        return json([
            'code' => 0,
            'msg' => '验证失败，请重试'
        ]);
    }
}
