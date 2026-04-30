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

namespace app\common\service;


use app\common\enum\FileEnum;
use app\common\model\file\File;
use app\common\service\storage\Driver as StorageDriver;
use Exception;


class UploadService
{

    // 【安全增强】允许的 MIME 类型映射
    private static array $allowedMimeTypes = [
        'image' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/bmp'
        ],
        'video' => [
            'video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 
            'video/x-ms-wmv', 'video/x-flv', 'video/x-matroska', 'video/3gpp'
        ],
        'file' => [
            'application/pdf', 'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
            'application/x-gzip', 'text/plain', 'text/csv'
        ]
    ];

    // 【安全增强】危险文件扩展名黑名单
    private static array $dangerousExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'pht',
        'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'sh', 'bash',
        'exe', 'bat', 'cmd', 'com', 'msi', 'dll', 'so', 'dylib',
        'htaccess', 'htpasswd', 'ini', 'conf', 'sql'
    ];

    /**
     * @notes 安全验证文件
     * @param array $fileInfo 文件信息
     * @param string $type 文件类型 image/video/file
     * @return void
     * @throws Exception
     */
    private static function validateFileSecurity(array $fileInfo, string $type): void
    {
        $ext = strtolower($fileInfo['ext']);
        
        // 1. 检查危险扩展名
        if (in_array($ext, self::$dangerousExtensions)) {
            throw new Exception("禁止上传可执行文件：{$ext}");
        }
        
        // 2. 检查扩展名是否在允许列表
        $allowedExts = config("project.file_{$type}", []);
        if (!in_array($ext, $allowedExts)) {
            throw new Exception("不允许上传 {$ext} 类型的文件");
        }
        
        // 3. 检查 MIME 类型
        $mimeType = $fileInfo['type'] ?? '';
        $allowedMimes = self::$allowedMimeTypes[$type] ?? [];
        if (!empty($mimeType) && !empty($allowedMimes)) {
            if (!in_array($mimeType, $allowedMimes)) {
                throw new Exception("文件类型不合法，请上传正确的文件格式");
            }
        }
        
        // 4. 检查文件名是否包含特殊字符
        $fileName = $fileInfo['name'] ?? '';
        if (preg_match('/[<>:"|?*\x00-\x1f]/', $fileName)) {
            throw new Exception("文件名包含非法字符");
        }
    }

    /**
     * @notes 上传图片
     * @param $cid
     * @param int $user_id
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author 段誉
     * @date 2021/12/29 16:30
     */
    public static function image($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/images')
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 【安全增强】验证文件安全性
            self::validateFileSecurity($fileInfo, 'image');

            // 上传文件
            $saveDir = self::getUploadUrl($saveDir);
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'type'        => FileEnum::IMAGE_TYPE,
                'name'        => $fileInfo['name'],
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @notes 视频上传
     * @param $cid
     * @param int $user_id
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author 段誉
     * @date 2021/12/29 16:32
     */
    public static function video($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/video')
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 【安全增强】验证文件安全性
            self::validateFileSecurity($fileInfo, 'video');

            // 上传文件
            $saveDir = self::getUploadUrl($saveDir);
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'type'        => FileEnum::VIDEO_TYPE,
                'name'        => $fileInfo['name'],
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 上传文件
     * @param $cid
     * @param int $sourceId
     * @param int $source
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author dw
     * @date 2023/06/26
     */
    public static function file($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/file')
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? [ 'local' => [] ],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 【安全增强】验证文件安全性
            self::validateFileSecurity($fileInfo, 'file');

            // 上传文件
            $saveDir = self::getUploadUrl($saveDir);
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name']) - 5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'type'        => FileEnum::FILE_TYPE,
                'name'        => $fileInfo['name'],
                'uri'         => $saveDir . '/' . str_replace("\\", "/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 上传地址
     * @param $saveDir
     * @return string
     * @author dw
     * @date 2023/06/26
     */
    private static function getUploadUrl($saveDir):string
    {
        return $saveDir . '/' . date('Ymd');
    }
}
