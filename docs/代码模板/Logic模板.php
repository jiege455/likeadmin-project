<?php

namespace app\adminapi\logic\{{module}};

use app\common\logic\BaseLogic;
use app\common\model\{{module}}\{{name}};
use think\facade\Db;

/**
 * {{title}}逻辑层
 * @author 杰哥
 * @date {{date}}
 */
class {{name}}Logic extends BaseLogic
{
    /**
     * @notes 添加{{title}}
     * @param array $params
     * @return bool|int
     * @author 杰哥
     * @date {{date}}
     */
    public static function add(array $params)
    {
        try {
            Db::startTrans();
            
            $data = [
                // TODO: 填写字段
            ];
            
            $result = {{name}}::create($data);
            
            Db::commit();
            return $result->id;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 编辑{{title}}
     * @param array $params
     * @return bool
     * @author 杰哥
     * @date {{date}}
     */
    public static function edit(array $params): bool
    {
        try {
            Db::startTrans();
            
            $data = [
                'id' => $params['id'],
                // TODO: 填写字段
            ];
            
            {{name}}::update($data);
            
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 获取{{title}}详情
     * @param int $id
     * @return array
     * @author 杰哥
     * @date {{date}}
     */
    public static function detail(int $id): array
    {
        $data = {{name}}::findOrEmpty($id);
        if ($data->isEmpty()) {
            return [];
        }
        return $data->toArray();
    }

    /**
     * @notes 删除{{title}}
     * @param int $id
     * @return bool
     * @author 杰哥
     * @date {{date}}
     */
    public static function delete(int $id): bool
    {
        try {
            {{name}}::destroy($id);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 切换{{title}}状态
     * @param int $id
     * @return bool
     * @author 杰哥
     * @date {{date}}
     */
    public static function status(int $id): bool
    {
        try {
            $data = {{name}}::findOrEmpty($id);
            if ($data->isEmpty()) {
                self::setError('数据不存在');
                return false;
            }
            
            $data->status = $data->status ? 0 : 1;
            $data->save();
            
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
