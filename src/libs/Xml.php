<?php

namespace mon\env\libs;

/**
 * xml解析
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Xml
{
    /**
     * 解析配置
     *
     * @param  string $config xml串，或者文件路径
     * @return array
     */
    public function parse($config)
    {
        if (is_file($config)) {
            $content = simplexml_load_file($config);
        } else {
            $content = simplexml_load_string($config);
        }
        $result = (array) $content;
        foreach ($result as $key => $val) {
            if (is_object($val)) {
                $result[$key] = (array) $val;
            }
        }

        return $result;
    }
}
