<?php

require __DIR__ . '/../vendor/autoload.php';

$config = \mon\env\Config::instance();

// set
$res = $config->set('test1', 1);
$res = $config->set('test2', [1, 2, 'a' => 3]);
$res = $config->set('test3.a', 111);
$res = $config->set(['demo1' => 1, 'demo2' => 2]);


// get all
$res = $config->get();
// get item
$res = $config->get('demo2');
// get default
$res = $config->get('demo2', 'aa');


// has
$exists = $config->has('test2');
$exists = $config->has('test2.a');


// clear
$config->clear();
$res = $config->get();


// loadFile 加载配置文件
// $config->loadFile(__DIR__ . '/config/php.php');            // 数组类型
// $config->loadFile(__DIR__ . '/config/ini.ini', '', 'ini');     // ini类型
// $config->loadFile(__DIR__ . '/config/xml.xml', 'xml');     // xml类型
// $config->loadFile(__DIR__ . '/config/yaml.yaml');           // yaml类型
// $config->loadFile(__DIR__ . '/../composer.json', 'json');   // json类型

// loadDir 加载配置文件目录

$config->loadDir(__DIR__  . '/config');

$res = $config->get();


var_dump($res);
