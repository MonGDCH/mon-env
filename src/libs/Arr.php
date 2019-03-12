<?php
namespace mon\env\libs;

/**
 * 数组解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Arr
{
    /**
     * 解析配置
     *
     * @param  [type] $config 数组，或者文件路径
     * @return [type]         [description]
     */
    public function parse($config)
    {
        if(is_file($config)){
            $config = include $config;
        }

        return $config;
    }
}
