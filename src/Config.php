<?php
namespace Mon\Env;

use Exception;

/**
 * 配置信息类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.2 增加xml、ini、json、yaml等解析驱动
 * @version 1.3 增加环境设置
 */
class Config
{
    /**
     * 单例实现
     *
     * @var [type]
     */
    protected static $instance;

    /**
     * 存储配置信息
     *
     * @var array
     */
    public $config = [];

    /**
     * 命名空间
     *
     * @var string
     */
    public $env;

    /**
     * 驱动类型
     *
     * @var [type]
     */
    protected $drive = [
        'arr', 'ini', 'json', 'xml', 'yaml'
    ];

    /**
     * 获取单例
     *
     * @param  string $env 环境命名空间
     * @return [type]      [description]
     */
    public static function instance(string $env = 'prd')
    {
        if(!self::$instance){
            self::$instance = new self($env);
        }

        return self::$instance;
    }

    /**
     * 私有化构造方法
     * 
     * @param string $env 环境命名空间
     */
    protected function __construct(string $env = 'prd')
    {
        $this->env($env);
    }

    /**
     * 环境设置
     *
     * @param  string $env 环境命名空间
     * @return [type]      [description]
     */
    public function env(string $env)
    {
        $this->env = $env;
        if(!isset($this->config[$this->env])){
            $this->config[$this->env] = [];
        }

        return $this;
    }

    /**
     * 注册配置
     *
     * @param  array  $config 配置信息
     * @return [type]         [description]
     */
    public function register(array $config, string $env = '')
    {
        $env = $env ?: $this->env;

        // 合并获取配置信息
        $this->config[$env] = array_merge($this->config[$env], $config);
        return $this->config[$env];
    }

    /**
     * 加载配置文件
     *
     * @param  [type] $config 扩展配置文件名
     * @param  [type] $alias  配置节点别名
     * @return [type]         [description]
     */
    public function load(string $file, string $alias = '')
    {
        if(!file_exists($file)){
            throw new Exception("config file not found! [{$file}]");
        }

        $type = pathinfo($file, PATHINFO_EXTENSION);
        $type = $type == 'php' ? 'arr' : $type;

        return $this->parse($file, $type, $alias);
    }

    /**
     * 解析配置
     *
     * @param  [type] $config [description]
     * @param  string $type   [description]
     * @param  string $alias  [description]
     * @return [type]         [description]
     */
    public function parse($config, string $type, string $alias = '')
    {
        if(!in_array(strtolower($type), $this->drive)){
            throw new Exception("config type is not supported");
        }
        $class = (false !== strpos($type, '\\')) ? $type : '\\mon\\env\\libs\\' . ucwords($type);
        $config = (new $class())->parse($config);

        if(empty($alias)){
            return $this->set($config);
        }

        return $this->set($alias, $config);
    }

    /**
     * 动态设置配置信息, 最多通过'.'分割设置2级配置
     *
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
    public function set($key, $value = null, $env = '')
    {
        $env = $env ?: $this->env;
        if(is_array($key)){
            // 数组，批量注册
            return $this->register($key, $env);
        }
        elseif(is_string($key)){
            // 字符串，节点配置
            if (!strpos($key, '.')) {
                $this->config[$env][$key] = $value;
            }
            else{
                $name = explode('.', $key, 2);
                $this->config[$env][$name[0]][$name[1]] = $value;
            }

        }
        return $value;
    }

    /**
     * 获取配置信息内容, 可以通过'.'分割获取无限级节点数据
     *
     * @param  [type] $key     [description]
     * @param  [type] $default [description]
     * @return [type]          [description]
     */
    public function get(string $key = '', $default = null, string $env = '')
    {
        $env = $env ?: $this->env;
        if(empty($key)){
            return $this->config[$env];
        }
        // 以"."分割，支持多纬度配置信息获取
        $name = explode('.', $key);
        $data = $this->config[$env];
        for($i = 0, $len = count($name); $i < $len; $i++)
        {
            // 不存在配置节点，返回默认值
            if(!isset($data[ $name[$i] ])){
                $data = $default;
                break;
            }
            $data = $data[ $name[$i] ];
        }

        return $data;
    }

    /**
     * 判断配置节点是否存在
     *
     * @param  string  $name [description]
     * @param  string  $env  [description]
     * @return boolean       [description]
     */
    public function has(string $key, string $env = '')
    {
        $env = $env ?: $this->env;
        if(!strpos($key, '.')){
            return isset($this->config[$env][$key]);
        }

        // 以"."分割，支持多纬度配置信息获取
        $name = explode('.', $key);
        $data = $this->config[$env];
        for($i = 0, $len = count($name); $i < $len; $i++)
        {
            // 不存在配置节点，返回默认值
            if(!isset($data[$name[$i]])){
                return false;
            }
            $data = $data[$name[$i]];
        }

        return true;
    }

    /**
     * 清空配置信息
     *
     * @return [type] [description]
     */
    public function clear($env = '')
    {
        $env = $env ?: $this->env;
        if($env === true){
            $this->config = [];
        }
        $this->config[$env] = [];

        return $this;
    }
}