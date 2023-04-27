<?php

namespace mon\env;

use ErrorException;

/**
 * 环境配置 .env 文件驱动
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Env
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected static $config = [];

    /**
     * 获取配置内容
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = '', $default = null)
    {
        if (empty($key)) {
            return static::$config;
        }

        return isset(static::$config[$key]) ? static::$config[$key] : $default;
    }

    /**
     * 加载定义配置文件
     *
     * @param string $file
     * @return void
     */
    public static function load($file = '.env')
    {
        if (!file_exists($file)) {
            throw new ErrorException('Env config file not exists! file: ' . $file);
        }
        // 读取配置文件
        $list = file($file);
        // 解析加载配置信息
        static::$config = static::formatValue($list);
    }

    /**
     * 解析配置信息
     *
     * @param array $list   配置列表
     * @return array
     */
    protected static function formatValue(array $list)
    {
        $result = [];
        foreach ($list as $line) {
            $item = trim($line);
            if (empty($item)) {
                continue;
            }
            $data = explode('=', $item, 2);
            $key = trim($data[0]);
            // 不存在， null值
            if (!isset($data[1])) {
                $result[$key] = null;
                continue;
            }
            $val = trim($data[1]);
            // 空字符串
            if (empty($val) && $val != 0) {
                $result[$key] = $val;
                continue;
            }
            // 起始符
            $pos = strpos($val, '0');
            // 判断整形0值
            if ($pos === 0 && $val == '0') {
                $result[$key] = 0;
                continue;
            }
            // 判断整数
            if ($pos !== 0 && (is_numeric($val) && is_int($val + 0))) {
                $result[$key] = intval($val);
                continue;
            }
            // 判断浮点数
            if ($pos !== 0 && (filter_var($val, FILTER_VALIDATE_FLOAT) !== false)) {
                $result[$key] = floatval($val);
                continue;
            }
            // 小写值
            $str = strtolower($val);
            // 判断 null
            if ($str == 'null') {
                $result[$key] = null;
                continue;
            }
            // 判断 true
            if ($str == 'true') {
                $result[$key] = true;
                continue;
            }
            // 判断 false
            if ($str == 'false') {
                $result[$key] = false;
                continue;
            }
            // 字符串
            $result[$key] = $val;
        }

        return $result;
    }
}
