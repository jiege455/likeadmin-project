<?php

namespace app\adminapi\validate\{{module}};

use app\common\validate\BaseValidate;

/**
 * {{title}}验证器
 * @author 杰哥
 * @date {{date}}
 */
class {{name}}Validate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        // TODO: 填写验证规则
        // 'name' => 'require|length:1,64',
        // 'status' => 'in:0,1',
    ];

    protected $message = [
        'id.require' => '请选择数据',
        'id.integer' => 'ID必须是整数',
        'id.gt' => 'ID必须大于0',
        // TODO: 填写错误提示
        // 'name.require' => '请输入名称',
        // 'name.length' => '名称长度为1-64个字符',
        // 'status.in' => '状态值不正确',
    ];

    /**
     * @notes 详情场景
     * @return {{name}}Validate
     * @author 杰哥
     * @date {{date}}
     */
    public function sceneDetail(): {{name}}Validate
    {
        return $this->only(['id']);
    }

    /**
     * @notes 添加场景
     * @return {{name}}Validate
     * @author 杰哥
     * @date {{date}}
     */
    public function sceneAdd(): {{name}}Validate
    {
        return $this->only([
            // TODO: 填写字段
            // 'name',
            // 'status',
        ]);
    }

    /**
     * @notes 编辑场景
     * @return {{name}}Validate
     * @author 杰哥
     * @date {{date}}
     */
    public function sceneEdit(): {{name}}Validate
    {
        return $this->only([
            'id',
            // TODO: 填写字段
            // 'name',
            // 'status',
        ]);
    }

    /**
     * @notes 删除场景
     * @return {{name}}Validate
     * @author 杰哥
     * @date {{date}}
     */
    public function sceneDelete(): {{name}}Validate
    {
        return $this->only(['id']);
    }

    /**
     * @notes 状态场景
     * @return {{name}}Validate
     * @author 杰哥
     * @date {{date}}
     */
    public function sceneStatus(): {{name}}Validate
    {
        return $this->only(['id']);
    }
}
