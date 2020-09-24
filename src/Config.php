<?php

namespace mon\env;

use InvalidArgumentException;

/**
 * 配置信息类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 增加xml、ini、json、yaml等解析驱动
 * @version 1.0.2 调整代码，移除环境配置
 * @version 1.0.3 优化代码，降低版本要求为5.6
 * @version 1.0.4 优化代码，增强注解
 */
class Config
{
    /**
     * 单例实现
     *
     * @var Config
     */
    protected static $instance;

    /**
     * 存储配置信息
     *
     * @var array
     */
    public $config = [];

    /**
     * 驱动类型
     *
     * @var array
     */
    protected $drive = [
        'arr', 'ini', 'json', 'xml', 'yaml'
    ];

    /**
     * 获取单例
     *
     * @return Config
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 私有化构造方法
     */
    protected function __construct()
    {}

    /**
     * 注册配置
     *
     * @param  array  $config 配置信息
     * @return array 配置信息
     */
    public function register(array $config)
    {
        // 合并获取配置信息
        $this->config = array_merge($this->config, $config);
        return $this->config;
    }

    /**
     * 加载配置文件
     *
     * @param  string $config 配置文件路径
     * @param  string $alias  配置节点名称，空则表示全局
     * @return array 配置信息
     */
    public function load($file, $alias = '')
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException("config file not found! [{$file}]");
        }

        $type = pathinfo($file, PATHINFO_EXTENSION);
        $type = $type == 'php' ? 'arr' : $type;

        return $this->parse($file, $type, $alias);
    }

    /**
     * 解析配置
     *
     * @param  mixed  $config 配置文件路径或配置值
     * @param  string $type   配置类型，支持arr、ini、json、xml、yaml等格式
     * @param  string $alias  配置节点名称，空则表示全局
     * @return array 配置信息
     */
    public function parse($config, $type, $alias = '')
    {
        if (!in_array(strtolower($type), $this->drive)) {
            throw new InvalidArgumentException("config type is not supported");
        }
        $class = (false !== strpos($type, '\\')) ? $type : '\\mon\\env\\libs\\' . ucwords($type);
        $config = (new $class())->parse($config);

        if (empty($alias)) {
            return $this->set($config);
        }

        return $this->set($alias, $config);
    }

    /**
     * 动态设置配置信息, 最多通过'.'分割设置2级配置
     *
     * @param mixed  $key   数组代码重新设置配置信息，字符串则修改指定的配置键值（支持.分割多级配置）
     * @param mixed  $value 配置值，当key值为字符串类型是有效
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            // 数组，批量注册
            return $this->register((array)$key);
        } elseif (is_string($key)) {
            // 字符串，节点配置
            if (!strpos($key, '.')) {
                $this->config[$key] = $value;
            } else {
                $name = explode('.', $key, 2);
                $this->config[$name[0]][$name[1]] = $value;
            }
        }
        return $value;
    }

    /**
     * 获取配置信息内容, 可以通过'.'分割获取无限级节点数据
     *
     * @param  string $key     配置键名（支持.分割多级配置），空则获取所有配置信息
     * @param  mixed  $default 默认值
     * @return mixed 配置信息
     */
    public function get($key = '', $default = null)
    {
        if (empty($key)) {
            return $this->config;
        }
        // 以"."分割，支持多纬度配置信息获取
        $name = explode('.', $key);
        $data = $this->config;
        for ($i = 0, $len = count($name); $i < $len; $i++) {
            // 不存在配置节点，返回默认值
            if (!isset($data[$name[$i]])) {
                $data = $default;
                break;
            }
            $data = $data[$name[$i]];
        }

        return $data;
    }

    /**
     * 判断配置节点是否存在
     *
     * @param  string  $name 配置键名（支持.分割多级配置）
     * @return boolean
     */
    public function has($key)
    {
        if (!strpos($key, '.')) {
            return isset($this->config[$key]);
        }

        // 以"."分割，支持多纬度配置信息获取
        $name = explode('.', $key);
        $data = $this->config;
        for ($i = 0, $len = count($name); $i < $len; $i++) {
            // 不存在配置节点，返回默认值
            if (!isset($data[$name[$i]])) {
                return false;
            }
            $data = $data[$name[$i]];
        }

        return true;
    }

    /**
     * 清空配置信息
     *
     * @return Config
     */
    public function clear()
    {
        $this->config = [];

        return $this;
    }
}
