<?php
/**
 * XSS过滤助手
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\common\utils;

class XssFilter
{
    private static $evilTags = [
        'script', 'iframe', 'frame', 'frameset', 'object', 'embed',
        'applet', 'meta', 'link', 'style', 'base', 'form',
    ];

    private static $evilAttrs = [
        'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout',
        'onkeydown', 'onkeyup', 'onkeypress', 'onfocus', 'onblur',
        'onsubmit', 'onreset', 'onchange', 'onselect', 'onabort',
        'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover',
        'ondragstart', 'ondrop', 'oncontextmenu', 'onscroll',
    ];

    public static function clean($string)
    {
        if (empty($string) || !is_string($string)) {
            return $string;
        }

        $string = self::removeNullBytes($string);
        $string = self::removeEvilTags($string);
        $string = self::removeEvilAttributes($string);
        $string = self::removeDangerousProtocols($string);
        $string = htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $string;
    }

    private static function removeNullBytes($string)
    {
        return str_replace("\0", '', $string);
    }

    private static function removeEvilTags($string)
    {
        foreach (self::$evilTags as $tag) {
            $pattern = '/<\s*\/?\s*' . $tag . '[^>]*>/is';
            $string = preg_replace($pattern, '', $string);
        }
        return $string;
    }

    private static function removeEvilAttributes($string)
    {
        foreach (self::$evilAttrs as $attr) {
            $pattern = '/\s+' . $attr . '\s*=\s*["\'][^"\']*["\']/is';
            $string = preg_replace($pattern, '', $string);
            $pattern = '/\s+' . $attr . '\s*=\s*[^\s>]*/is';
            $string = preg_replace($pattern, '', $string);
        }
        return $string;
    }

    private static function removeDangerousProtocols($string)
    {
        $dangerousProtocols = ['javascript', 'vbscript', 'data', 'mhtml'];
        foreach ($dangerousProtocols as $protocol) {
            $pattern = '/(' . $protocol . ')\s*:/is';
            $string = preg_replace($pattern, '$1&#58;', $string);
        }
        return $string;
    }

    public static function cleanArray(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::cleanArray($value);
            } elseif (is_string($value)) {
                $result[$key] = self::clean($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function cleanHtml($string, $allowedTags = '<p><br><strong><em><u><span><div><ul><ol><li><img><a>')
    {
        if (empty($string) || !is_string($string)) {
            return $string;
        }

        $string = self::removeNullBytes($string);
        $string = self::removeEvilAttributes($string);
        $string = self::removeDangerousProtocols($string);
        $string = strip_tags($string, $allowedTags);

        return $string;
    }
}
