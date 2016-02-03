<?php

/**
 * 配置操作类
 */

namespace Core;

class Config
{

    /**
     * 根据给定的key获取配置
     * @param string $fileName 文件名称
     * @param string $confKey 配置数组的key
     * @return array
     */
    public static function getConfByKey($fileName, $confKey)
    {
        $confArr = self::includeConfFile($fileName);
        return isset($confArr[$confKey]) ? $confArr[$confKey] : array();
    }

    /**
     * 根据给定的key,随机获取其中的一个获取配置
     * @param string $fileName 文件名称
     * @param string $confKey 配置数组的key
     * @return array
     */
    public static function randGetConfByKey($fileName, $confKey)
    {
        $confArr = self::includeConfFile($fileName);
        $res = isset($confArr[$confKey]) ? $confArr[$confKey] : array();
        $randRes = array();
        if (!empty($res)) {
            $lastKey = count($res) - 1;
            $randIndex = rand(0, $lastKey);
            $randRes = $res[$randIndex];
        }
        return $randRes;
    }

    /**
     * 加载配置文件,获取返回的数组
     * @param string $fileName 需要加载的配置文件名,不包括后缀
     * @return array
     */
    public static function includeConfFile($fileName)
    {
        $result = array();
        if ('pro'==ENV) {
            $result = require CONF_PRO_PATH.$fileName.'.php';
        } else {
            $result = require CONF_DEV_PATH.$fileName.'.php';
        }
        return $result;
    }
}
