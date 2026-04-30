<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class OpenResultSync extends Command
{
    protected function configure()
    {
        $this->setName('openResult:sync')
            ->setDescription('同步开奖结果');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开始同步开奖结果...');

        $lotteryTypes = ['fc3d', 'pl3', 'ssq', 'dlt'];
        $syncCount = 0;
        $errorCount = 0;

        foreach ($lotteryTypes as $type) {
            try {
                $result = $this->fetchOpenResult($type);
                if ($result) {
                    foreach ($result as $item) {
                        $syncResult = $this->syncOne($type, $item);
                        if ($syncResult) {
                            $syncCount++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                $output->writeln("同步{$type}失败: " . $e->getMessage());
            }
        }

        $output->writeln("同步完成: 成功{$syncCount}条, 失败{$errorCount}条");
    }

    private function fetchOpenResult($lotteryType)
    {
        $apiUrl = env('OPEN_RESULT_API_URL', '');
        $apiKey = env('OPEN_RESULT_API_KEY', '');

        if (empty($apiUrl)) {
            return [];
        }

        $url = $apiUrl . '?lottery_type=' . $lotteryType . '&key=' . $apiKey;
        
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function syncOne($lotteryType, $item)
    {
        $issueNo = $item['issue_no'] ?? '';
        $openCode = $item['open_code'] ?? '';
        $openTime = $item['open_time'] ?? time();

        if (empty($issueNo) || empty($openCode)) {
            return false;
        }

        $series = Db::name('article_cate')
            ->where('lottery_type', $lotteryType)
            ->where('is_series', 1)
            ->where('delete_time', null)
            ->select()
            ->toArray();

        $synced = false;
        foreach ($series as $s) {
            $article = Db::name('article')
                ->where('series_id', $s['id'])
                ->where('issue_no', $issueNo)
                ->where('is_opened', 0)
                ->where('delete_time', null)
                ->find();

            if ($article) {
                Db::name('article')->where('id', $article['id'])->update([
                    'is_opened' => 1,
                    'open_code' => $openCode,
                    'open_time' => is_numeric($openTime) ? $openTime : strtotime($openTime),
                    'issue_status' => 2,
                    'update_time' => time()
                ]);

                Db::name('open_result_log')->insert([
                    'lottery_type' => $lotteryType,
                    'issue_no' => $issueNo,
                    'open_code' => $openCode,
                    'open_time' => is_numeric($openTime) ? $openTime : strtotime($openTime),
                    'sync_status' => 1,
                    'sync_time' => time(),
                    'matched_series_id' => $s['id'],
                    'matched_article_id' => $article['id'],
                    'create_time' => time()
                ]);

                $synced = true;
            }
        }

        if (!$synced) {
            Db::name('open_result_log')->insert([
                'lottery_type' => $lotteryType,
                'issue_no' => $issueNo,
                'open_code' => $openCode,
                'open_time' => is_numeric($openTime) ? $openTime : strtotime($openTime),
                'sync_status' => 0,
                'sync_time' => time(),
                'error_msg' => '未匹配到期次',
                'create_time' => time()
            ]);
        }

        return $synced;
    }
}
