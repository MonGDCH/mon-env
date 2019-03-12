# mon-env

基于PHP7的配置管理工具, 只要实现以下功能:

* 支持运行环境判定, 用于区分开发环境、生产环境等
* 多种配置文件格式支持, 支持array、json、ini、xml、yaml等类型

## 安装

```
composer require mongdch/mon-env
```

## 文档说明

#### 设置当前环境
> Config env(string $env = 'prd')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| env | 否  | string | 当前运行环境标识符，默认prd |


例子：

```php
// 获取单例时设置
$instance = \mon\env\Config::instance('dev');

// 调用env方法设置
$instance = \mon\env\Config::instance()->env('dev');

```

#### 注册配置
> array register(array $config, string $env = '')

注意：register方法只支持直接的数组注册

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| config | 是  | array | 注册的配置信息 |
| env | 否  | string | 注册配置信息的对应环境，默认当前环境 |

例子：

```php
// 获取单例时设置
$config = \mon\env\Config::instance()->register($config, $env);

```

#### 加载配置文件
> array load(string $file, string $alias = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| file | 是  | string | 配置文件路径 |
| alias | 否  | string | 配置信息别名 |

例子：

```php
// 获取单例时设置
$config = \mon\env\Config::instance()->load($file, $aliasName);

```

#### 解析配置文件
> array parse($config, string $type, string $alias = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| config | 是  | data | 待解析的配置，可文件路径，可配置信息 |
| type | 是  | string | 解析类型[arr、json、ini、xml、yaml] |
| alias | 否  | string | 配置信息别名 |

例子：

```php
// 获取单例时设置
$config = \mon\env\Config::instance()->parse($file, 'arr', $aliasName);

```

#### 设置配置信息
> array set($key, $value = null, $env = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 是  | array|string | 配置信息，或配置节点名称 |
| value | 否  | data | 配置信息 |
| env | 否  | string | 配置信息的对应环境，默认当前环境 |

例子：

```php
// 设置数据
\mon\env\Config::instance()->set('test1', 1);
\mon\env\Config::instance()->set('test2', [1, 2, 'a' => 3]);
\mon\env\Config::instance()->set(['demo1' => 1, 'demo2' => 2]);

```

#### 获取配置信息
> data get(string $key = '', $default = null, string $env = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 否  | string | 配置节点名称, 默认返回当前环境下所有配置 |
| default | 否  | data | 默认值 |
| env | 否  | string | 配置信息的对应环境，默认当前环境 |

例子：

```php
// 获取所有
$res = $config->get();
// 获取指定节点
$res = $config->get('demo2');
// 获取多级节点
$res = $config->get('demo2.demo.a');
// 设置默认值
$res = $config->get('demo3', 'aa');

```

#### 判断配置信息是否存在
> bool has(string $key, string $env = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 是  | string | 配置节点名称 |
| env | 否  | string | 配置信息的对应环境，默认当前环境 |

例子：

```php
// 判断指定节点
$exists = $config->has('test2');
// 多级多维数组节点
$exists = $config->has('test2.a');

```

#### 清空配置信息
> Config clear($env = '')

参数说明：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| env | 否  | bool|string | 配置信息的对应环境，默认当前环境，为true时，清空所有配置 |

例子：

```php
// 清楚当前环境配置信息
$config->clear();
// 清楚指定环境配置信息
$config->clear('dev');
// 清楚所有环境配置信息
$config->clear(true);
```


---

# 版本

### 1.0.0

* 发布第一个LTS版本


---

# 致谢

感谢您的支持和阅读，如果有什么不足的地方或者建议还请@我，如果你觉得对你有帮助的话还请给个star。

---

# 关于

作者邮箱： 985558837@qq.com

作者博客： [http://blog.gdmon.com](http://blog.gdmon.com)
