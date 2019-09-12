<?php

namespace mon\env\libs;

use BadFunctionCallException;

/**
 * yaml解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Yaml
{
    /**
     * 解析配置
     *
     * @param  [type] $config yaml数据，或者文件路径
     * @return [type]         [description]
     */
    public function parse($config)
    {
        if (!function_exists('yaml_parse_file') || !function_exists('yaml_parse')) {
            throw new BadFunctionCallException("PECL-yaml is not installed.");
        }

        if (is_file($config)) {
            $config = yaml_parse_file($config);
        } else {
            $config = yaml_parse($config);
        }

        return $config;
    }
}
