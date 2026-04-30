<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------
namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\user\UserTerminalEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\user\User;
use think\facade\Db;


/**
 * 用户列表
 * Class UserLists
 * @package app\adminapi\lists\user
 */
class UserLists extends BaseAdminDataLists implements ListsExcelInterface
{

    /**
     * @notes 搜索条件
     * @return array
     * @author 段誉
     * @date 2022/9/22 15:50
     */
    public function setSearch(): array
    {
        $allowSearch = ['keyword', 'channel', 'create_time_start', 'create_time_end'];
        return array_intersect(array_keys($this->params), $allowSearch);
    }


    /**
     * @notes 获取用户列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/22 15:50
     */
    public function lists(): array
    {
        $field = "id,sn,nickname,sex,avatar,account,mobile,channel,inviter_id,create_time";
        $lists = User::withSearch($this->setSearch(), $this->params)
            ->limit($this->limitOffset, $this->limitLength)
            ->field($field)
            ->order('id desc')
            ->select()->toArray();

        // 批量查询商家状态
        $userIds = array_column($lists, 'id');
        $merchantMap = [];
        if (!empty($userIds)) {
            $merchants = Db::name('merchant')
                ->whereIn('user_id', $userIds)
                ->where('status', 1)
                ->column('id', 'user_id');
            $merchantMap = $merchants;
        }

        // 批量查询邀请人信息（修复N+1查询）
        $inviterIds = array_filter(array_unique(array_column($lists, 'inviter_id')));
        $inviterMap = [];
        if (!empty($inviterIds)) {
            $inviters = User::whereIn('id', $inviterIds)
                ->field('id,nickname')
                ->select()
                ->toArray();
            foreach ($inviters as $inviter) {
                $inviterMap[$inviter['id']] = $inviter['nickname'];
            }
        }

        foreach ($lists as &$item) {
            $item['channel'] = UserTerminalEnum::getTermInalDesc($item['channel']);
            // 获取上级信息（使用预加载的数据）
            $item['inviter_nickname'] = '无';
            if ($item['inviter_id'] > 0 && isset($inviterMap[$item['inviter_id']])) {
                $item['inviter_nickname'] = $inviterMap[$item['inviter_id']] . " (ID:{$item['inviter_id']})";
            }
            // 判断是否商家
            $item['is_merchant'] = isset($merchantMap[$item['id']]) ? 1 : 0;
        }

        return $lists;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author 段誉
     * @date 2022/9/22 15:51
     */
    public function count(): int
    {
        return User::withSearch($this->setSearch(), $this->params)->count();
    }


    /**
     * @notes 导出文件名
     * @return string
     * @author 段誉
     * @date 2022/11/24 16:17
     */
    public function setFileName(): string
    {
        return '用户列表';
    }


    /**
     * @notes 导出字段
     * @return string[]
     * @author 段誉
     * @date 2022/11/24 16:17
     */
    public function setExcelFields(): array
    {
        return [
            'sn' => '用户编号',
            'nickname' => '用户昵称',
            'account' => '账号',
            'mobile' => '手机号码',
            'channel' => '注册来源',
            'create_time' => '注册时间',
        ];
    }

}