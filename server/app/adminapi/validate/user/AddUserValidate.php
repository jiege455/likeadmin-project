<?php
namespace app\adminapi\validate\user;

use app\common\validate\BaseValidate;
use app\common\model\user\User;

class AddUserValidate extends BaseValidate
{
    protected $rule = [
        'account' => 'require|length:3,20|alphaNum|unique:'.User::class,
        'password' => 'require|length:6,20',
        'password_confirm' => 'require|confirm:password',
        'nickname' => 'max:32',
        'mobile' => 'mobile',
    ];

    protected $message = [
        'account.require' => '请输入账号',
        'account.length' => '账号长度为3-20位',
        'account.alphaNum' => '账号只能是字母和数字',
        'account.unique' => '账号已存在',
        'password.require' => '请输入密码',
        'password.length' => '密码长度为6-20位',
        'password_confirm.require' => '请输入确认密码',
        'password_confirm.confirm' => '两次密码输入不一致',
        'nickname.max' => '昵称最长32位',
        'mobile.mobile' => '手机号码格式错误',
    ];
}