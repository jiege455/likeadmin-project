<?php

namespace app\common\model\{{module}};

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * {{title}}模型
 * @author 杰哥
 * @date {{date}}
 */
class {{name}} extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    /**
     * @notes 关联XXX
     * @return \think\model\relation\BelongsTo
     * @author 杰哥
     * @date {{date}}
     */
    public function xxx()
    {
        // return $this->belongsTo(Xxx::class, 'xxx_id');
    }

    /**
     * @notes 关键词搜索器
     * @param $query
     * @param $value
     * @param $data
     * @author 杰哥
     * @date {{date}}
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * @notes 状态搜索器
     * @param $query
     * @param $value
     * @param $data
     * @author 杰哥
     * @date {{date}}
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '') {
            $query->where('status', '=', $value);
        }
    }

    /**
     * @notes 状态获取器
     * @param $value
     * @param $data
     * @return string
     * @author 杰哥
     * @date {{date}}
     */
    public function getStatusTextAttr($value, $data): string
    {
        $statusMap = [
            0 => '禁用',
            1 => '正常',
        ];
        return $statusMap[$data['status']] ?? '未知';
    }
}
