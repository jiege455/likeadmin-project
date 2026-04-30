<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\common\service\FileService;
use think\facade\Db;

/**
 * 商家信息控制器
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class InfoController extends BaseApiController
{
    public function get()
    {
        $merchant = Db::name('merchant')
            ->alias('m')
            ->leftJoin('user u', 'm.user_id = u.id')
            ->field('m.*, u.avatar as user_avatar')
            ->where('m.user_id', $this->userId)
            ->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $logo = $merchant['logo'] ?? '';
        if ($logo) {
            $logo = FileService::getFileUrl($logo);
        } else {
            $image = $merchant['image'] ?? '';
            if ($image) {
                $logo = FileService::getFileUrl($image);
            } else {
                $userAvatar = $merchant['user_avatar'] ?? '';
                if ($userAvatar) {
                    $logo = FileService::getFileUrl($userAvatar);
                }
            }
        }

        return $this->data([
            'id' => $merchant['id'],
            'name' => $merchant['name'] ?? '',
            'mobile' => $merchant['mobile'] ?? '',
            'wechat' => $merchant['wechat'] ?? '',
            'intro' => $merchant['intro'] ?? '',
            'logo' => $logo,
            'money' => $merchant['money'] ?? 0,
            'status' => $merchant['status'] ?? 0,
        ]);
    }

    public function set()
    {
        $post = $this->request->post();
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $logo = $post['logo'] ?? '';
        if ($logo) {
            $baseUrl = request()->domain();
            if (strpos($logo, $baseUrl) === 0) {
                $logo = str_replace($baseUrl, '', $logo);
            }
        }

        $name = $post['name'] ?? '';
        $data = [
            'name' => $name,
            'mobile' => $post['mobile'] ?? '',
            'wechat' => $post['wechat'] ?? '',
            'intro' => $post['intro'] ?? '',
            'logo' => $logo,
            'update_time' => time()
        ];

        Db::name('merchant')->where('id', $merchant['id'])->update($data);
        return $this->success('保存成功');
    }

    /**
     * @notes 获取推广链接
     */
    public function getPromotionLink()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $user = Db::name('user')->where('id', $this->userId)->find();
        if (empty($user['is_distributor'])) {
            return $this->fail('请先申请成为分销员', [], 20001);
        }

        $merchantId = $merchant['id'];
        $inviteCode = $user['sn'] ?? '';
        
        $baseUrl = request()->domain();
        $h5Url = $baseUrl . '/#/packages/pages/merchant/home?id=' . $merchantId;
        
        if ($inviteCode) {
            $h5Url .= '&invite_code=' . $inviteCode;
        }

        return $this->data([
            'merchant_id' => $merchantId,
            'invite_code' => $inviteCode,
            'h5_url' => $h5Url,
            'qrcode_url' => $this->generateQrcode($merchantId, $h5Url),
        ]);
    }

    /**
     * @notes 生成推广二维码（使用API方式）
     */
    private function generateQrcode($merchantId, $url)
    {
        $qrcodePath = 'uploads/qrcode/merchant/';
        $savePath = root_path('public') . $qrcodePath;
        
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        $fileName = 'merchant_' . $merchantId . '.png';
        $filePath = $savePath . $fileName;

        if (!file_exists($filePath)) {
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($url);
            $qrContent = @file_get_contents($qrApiUrl);
            
            if ($qrContent) {
                file_put_contents($filePath, $qrContent);
            } else {
                $googleApiUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($url) . '&choe=UTF-8';
                $qrContent = @file_get_contents($googleApiUrl);
                if ($qrContent) {
                    file_put_contents($filePath, $qrContent);
                }
            }
        }

        if (file_exists($filePath)) {
            return request()->domain() . '/' . $qrcodePath . $fileName . '?t=' . time();
        }
        
        return '';
    }
}
