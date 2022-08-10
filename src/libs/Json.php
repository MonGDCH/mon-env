<?php

namespace mon\env\libs;

use mon\env\interfaces\Handler;

/**
 * json解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Json implements Handler
{
    /**
     * 解析配置
     *
     * @param  string $config json串，或者文件路径
     * @return array
     */
    public function parse($config)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }

        return json_decode($config, true);
    }
}
