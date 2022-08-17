## 使用文档


#### 注册配置

> 批量覆盖写入配置信息，返回当前所有配置信息
> 注意：register方法只支持直接的数组注册

```php
register(array $config): array
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| config | 是  | array | 注册的配置信息 |

- 演示：

```php
$set = ['name' => 'demo config'];
$config = \mon\env\Config::instance()->register($set);
var_dump($config);
```

#### 加载配置文件

> 加载配置文件，作为全局配置或指定配置节点，返回配置文件配置信息

```php
loadFile(string $file, string $alias = '', string $type = ''): mixed
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| file | 是  | string | 配置文件路径 |
| alias | 否  | string | 配置节点名称，空则表示全局 |
| type | 否  | string | 驱动类型，默认文件扩展名 |


- 演示：

```php
$file = 'config.php';
$aliasName = 'config';
$driveType = 'php';
$config = \mon\env\Config::instance()->loadFile($file, $aliasName, $driveType);
var_dump($config);
```


#### 加载配置文件目录

> 加载目录下所有指定类型文件作为配置，返回配置信息

```php
loadDir(string $dir, bool $recursive = true, array $exts = [], string $alias = ''): mixed
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| dir | 是  | string | 配置文件目录路径 |
| recursive | 否  | bool | 是否递归所有目录 |
| exts | 否  | array | 配置文件扩展名白名单, 空则不限制 |
| alias | 否  | string | 配置节点名称，空则表示全局 |


- 演示：

```php
$dir = './config';
$recursive = true;
$aliasName = 'config';
$exts = ['php', 'ini', 'json', 'xml', 'yaml'];
$config = \mon\env\Config::instance()->loadDir($dir, $recursive, $exts, $aliasName);
var_dump($config);
```


#### 解析配置文件

> 解析配置文件或配置内容，返回解析结果集

```php
parse($config, string $type): array
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| config | 是  | mixed | 待解析的配置，可文件路径，可配置信息 |
| type | 是  | string | 解析类型：php、json、ini、xml、yaml |

- 演示：

```php
$file = './composer.json';
$config = \mon\env\Config::instance()->parse($file, 'json');
var_dump($config);
```

#### 设置配置信息

> 设置配置，支持`.`分割2级配置，返回配置信息

```php
set(string|array$key, $value = null): mixed
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 是  | array| 数组代码重新设置配置信息，字符串则修改指定的配置键值 |
| value | 否  | mixed | 配置值，当key值为字符串类型时有效 |

- 演示：

```php
\mon\env\Config::instance()->set('test1', 1);
\mon\env\Config::instance()->set('test2', [1, 2, 'a' => 3]);
\mon\env\Config::instance()->set('test3.a', '支持二级配置');
\mon\env\Config::instance()->set(['demo1' => 1, 'demo2' => 2]);
```


#### 获取配置信息

> 获取配置信息内容, 支持通过`.`分割获取无限级节点数据

```php
get(string $key = '', $default = null): mixed
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 否  | string | 配置节点名称, 默认返回当前环境下所有配置 |
| default | 否  | mixed | 默认值 |


- 演示：

```php
// 获取所有
$config = \mon\env\Config::instance()->get();
// 获取指定节点
$config = \mon\env\Config::instance()->get('demo2');
// 获取多级节点
$config = \mon\env\Config::instance()->get('demo2.demo');
// 设置默认值
$config = \mon\env\Config::instance()->get('demo3', 'aa');
```

#### 配置节点是否存在

> 配置节点是否存在， 支持通过`.`分割判断无限级节点

```php
has(string $key): bool
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| key | 是  | string | 配置节点名称 |

- 演示：

```php
// 判断指定节点
$exists = \mon\env\Config::instance()->has('test2');
// 多级多维数组节点
$exists = \mon\env\Config::instance()->has('test2.a');
```

#### 清空配置信息

> 清空所有配置信息，返回当前实例

```php
clear(): \mon\env\Config
```


- 演示：

```php
// 清楚当前所有配置信息
\mon\env\Config::instance()->clear();
```


#### 获取支持的解析驱动

> 获取当前支持的解析驱动名称及驱动对象

```php
drive(): array
```

- 演示：

```php
$driveTypes = \mon\env\Config::instance()->drive();
var_dump($driveTypes);
```


#### 扩展解析驱动

> 扩展自定义配置解析驱动

```php
extend(string $name, string $drive): Config
```

- 参数：

|参数名|是否必须|类型|说明|
|:----|:---|:----- |-----|
| name | 是  | string | 扩展驱动名称 |
| drive | 是  | string | 扩展驱动类名称，需实现`\mon\env\interfaces\Handler`接口 |


- 演示：

```php
// 自定义解析驱动
class UserDrive implements \mon\env\interfaces\Handler
{
    /**
     * 解析配置
     *
     * @param  array $config 配置内容
     * @return array
     */
    public function parse($config)
    {
        return $config
    }
}
// 扩展驱动
\mon\env\Config::instance()->extend('user', UserDrive::class);

var_dump(\mon\env\Config::instance()->drive());
```



