<?php

namespace mon\env;

use mon\env\libs\Arr;
use mon\env\libs\Ini;
use mon\env\libs\Xml;
use mon\env\libs\Json;
use mon\env\libs\Yaml;
use InvalidArgumentException;
use mon\env\interfaces\Handler;

/**
 * 配置信息类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.1 增加xml、ini、json、yaml等解析驱动
 * @version 1.0.2 调整代码，移除环境配置
 * @version 1.0.3 优化代码，降低版本要求为5.6
 * @version 1.0.4 优化代码，增强注解
 * @version 1.0.5 2022-08-10 优化架构，增加 extend 方法用于扩展解析驱动 
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
    protected $driveType = [
        'arr'   => Arr::class,
        'ini'   => Ini::class,
        'json'  => Json::class,
        'xml'   => Xml::class,
        'yaml'  => Yaml::class
    ];

    /**
     * 创建的驱动实例缓存
     *
     * @var Handler[]
     */
    protected $drive = [];

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
    {
    }

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
     * @param string $config 配置文件路径
     * @param string $alias  配置节点名称，空则表示全局
     * @param string $type   驱动类型，默认更新文件后缀名
     * @throws InvalidArgumentException
     * @return array 配置信息
     */
    public function load($file, $alias = '', $type = '')
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException("config file not found! [{$file}]");
        }

        // 获取驱动类型
        if (!$type) {
            $type = pathinfo($file, PATHINFO_EXTENSION);
            $type = $type == 'php' ? 'arr' : $type;
        }

        return $this->parse($file, $type, $alias);
    }

    /**
     * 解析配置
     *
     * @param  mixed  $config 配置文件路径或配置值
     * @param  string $type   配置类型，支持arr、ini、json、xml、yaml等格式
     * @param  string $alias  配置节点名称，空则表示全局
     * @throws InvalidArgumentException
     * @return array 配置信息
     */
    public function parse($config, $type, $alias = '')
    {
        if (!in_array(strtolower($type), array_keys($this->driveType))) {
            throw new InvalidArgumentException("config type is not supported");
        }

        // 加载驱动，解析配置
        if (!isset($this->drive[$type])) {
            $this->drive[$type] = new $this->driveType[$type];
        }
        $config = $this->drive[$type]->parse($config);

        return empty($alias) ? $this->set($config) : $this->set($alias, $config);
    }

    /**
     * 动态设置配置信息, 最多通过'.'分割设置2级配置
     *
     * @param mixed  $key   数组代码重新设置配置信息，字符串则修改指定的配置键值（支持.分割多级配置）
     * @param mixed  $value 配置值，当key值为字符串类型是有效
     * @return mixed    配置信息
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            // 数组，批量注册
            return $this->register($key);
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
     * 获取支持的驱动
     *
     * @return array
     */
    public function drive()
    {
        return $this->driveType;
    }

    /**
     * 扩展支持的驱动
     *
     * @param string $name  驱动名称
     * @param string $drive 驱动类名
     * @return Config
     */
    public function extend($name, $drive)
    {
        if (!$drive instanceof Handler) {
            throw new InvalidArgumentException('Drive needs implement the ' . Handler::class);
        }

        $this->driveType[$name] = $drive;
        return $this;
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
