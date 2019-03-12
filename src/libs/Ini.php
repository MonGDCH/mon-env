<?php
namespace mon\env\libs;

/**
 * Ini解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Ini
{
    /**
     * 解析配置
     *
     * @param  [type] $config 字符串，或者文件路径
     * @return [type]         [description]
     */
    public function parse($config)
    {
        if(is_file($config)){
            return parse_ini_file($config, true);
        }
            
        return parse_ini_string($config, true);
    }
}
