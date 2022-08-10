<?php

namespace mon\env\interfaces;

/**
 * 解析器接口
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
interface Handler
{
    /**
     * 解析配置
     *
     * @param mixed $config 配置信息
     * @return array
     */
    public function parse($config);
}
