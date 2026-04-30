<?php
namespace app\adminapi\logic\notice;

use app\common\logic\BaseLogic;
use app\common\model\notice\SystemNotice;

class SystemNoticeLogic extends BaseLogic
{
    public static function add($params)
    {
        SystemNotice::create([
            'title' => $params['title'],
            'content' => $params['content'],
            'recipient' => $params['recipient'] ?? 1,
            'is_show' => $params['is_show'] ?? 1,
            'popup_type' => $params['popup_type'] ?? 1,
            'type' => $params['type'] ?? 1,
            'is_top' => $params['is_top'] ?? 0,
            'cover' => $params['cover'] ?? '',
            'sort' => $params['sort'] ?? 0,
            'create_time' => time()
        ]);
    }

    public static function edit($params)
    {
        $data = [
            'id' => $params['id'],
            'title' => $params['title'],
            'content' => $params['content'],
            'recipient' => $params['recipient'] ?? 1,
            'is_show' => $params['is_show'] ?? 1,
            'popup_type' => $params['popup_type'] ?? 1,
            'type' => $params['type'] ?? 1,
            'is_top' => $params['is_top'] ?? 0,
            'cover' => $params['cover'] ?? '',
            'sort' => $params['sort'] ?? 0,
            'update_time' => time()
        ];
        SystemNotice::update($data);
    }

    public static function delete($params)
    {
        SystemNotice::destroy($params['id']);
    }

    public static function detail($id)
    {
        return SystemNotice::where('id', $id)
            ->findOrEmpty()
            ->toArray();
    }

    public static function lists($limitOffset, $limitLength)
    {
        $list = SystemNotice::limit($limitOffset, $limitLength)
            ->order(['is_top' => 'desc', 'sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
        $count = SystemNotice::count();
        return ['lists' => $list, 'count' => $count];
    }
}
