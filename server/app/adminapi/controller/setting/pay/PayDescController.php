<?php

namespace app\adminapi\controller\setting\pay;

use app\adminapi\controller\BaseAdminController;
use think\facade\Db;

class PayDescController extends BaseAdminController
{
    public function lists()
    {
        $configs = Db::name('config')
            ->where('type', 'pay_desc')
            ->select()
            ->toArray();
        
        $data = [
            'recharge_desc' => '会员充值',
            'article_desc' => '会员服务',
            'order_desc' => '商品订单',
        ];
        
        foreach ($configs as $config) {
            $data[$config['name']] = $config['value'];
        }
        
        return $this->data($data);
    }
    
    public function set()
    {
        $params = $this->request->post();
        
        $allowFields = ['recharge_desc', 'article_desc', 'order_desc'];
        
        foreach ($params as $name => $value) {
            if (!in_array($name, $allowFields)) {
                continue;
            }
            
            $exists = Db::name('config')
                ->where('type', 'pay_desc')
                ->where('name', $name)
                ->find();
            
            if ($exists) {
                Db::name('config')
                    ->where('type', 'pay_desc')
                    ->where('name', $name)
                    ->update([
                        'value' => $value,
                        'update_time' => time()
                    ]);
            } else {
                Db::name('config')->insert([
                    'type' => 'pay_desc',
                    'name' => $name,
                    'value' => $value,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
        }
        
        return $this->success('保存成功');
    }
}
