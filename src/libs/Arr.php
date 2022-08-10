<?php

namespace mon\env\libs;

use mon\env\interfaces\Handler;

/**
 * 数组解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Arr implements Handler
{
    /**
     * 解析配置
     *
     * @param  mixed $config 数组，或者文件路径
     * @return array
     */
    public function parse($config)
    {
        if (is_file($config)) {
            $config = include $config;
        }

        return $config;
    }
}
