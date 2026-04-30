<?php
// +----------------------------------------------------------------------
// | 滑块验证码控制器 - 后台管理端
// +----------------------------------------------------------------------
// | 开发者：杰哥网络科技
// | QQ: 2711793818
// +----------------------------------------------------------------------

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;

/**
 * 滑块验证码控制器
 */
class CaptchaController extends BaseAdminController
{
    public array $notNeedLogin = ['get', 'check'];

    /**
     * 获取验证码
     */
    public function get()
    {
        $captcha = new \SliderCaptcha();
        $data = $captcha->get();
        
        return $this->success('获取成功', $data);
    }

    /**
     * 验证滑块
     */
    public function check()
    {
        $key = input('key', '');
        $x = input('x', 0);
        
        if (empty($key)) {
            return $this->fail('验证码key不能为空');
        }
        
        $captcha = new \SliderCaptcha();
        $result = $captcha->check($key, (int)$x);
        
        if ($result) {
            return $this->success('验证成功', ['verified' => true]);
        }
        
        return $this->fail('验证失败，请重试');
    }
}
