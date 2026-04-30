<?php

namespace app\adminapi\lists\{{module}};

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\{{module}}\{{name}};

/**
 * {{title}}列表
 * @author 杰哥
 * @date {{date}}
 */
class {{name}}Lists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 设置搜索条件
     * @return array
     * @author 杰哥
     * @date {{date}}
     */
    public function setSearch(): array
    {
        return [
            // TODO: 填写允许搜索的字段
            // 'keyword',
            // 'status',
            // 'create_time_start',
            // 'create_time_end',
        ];
    }

    /**
     * @notes 获取列表数据
     * @return array
     * @author 杰哥
     * @date {{date}}
     */
    public function lists(): array
    {
        $lists = {{name}}::where($this->searchWhere)
            ->field([
                'id',
                // TODO: 填写字段
                'create_time',
            ])
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 获取总数
     * @return int
     * @author 杰哥
     * @date {{date}}
     */
    public function count(): int
    {
        return {{name}}::where($this->searchWhere)->count();
    }

    /**
     * @notes 设置导出文件名
     * @return string
     * @author 杰哥
     * @date {{date}}
     */
    public function setFileName(): string
    {
        return '{{title}}列表';
    }

    /**
     * @notes 设置导出字段
     * @return array
     * @author 杰哥
     * @date {{date}}
     */
    public function setExcelFields(): array
    {
        return [
            'id' => 'ID',
            // TODO: 填写导出字段
            'create_time' => '创建时间',
        ];
    }
}
