<?php

require __DIR__ . '/../vendor/autoload.php';

$config = \mon\env\Config::instance();

// set
$res = $config->set('test1', 1);
$res = $config->set('test2', [1, 2, 'a' => 3]);
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


// load 加载配置文件
$config->load(__DIR__ . '/config/test.php');    // 数组类型
$config->load(__DIR__ . '/config/test.ini', 'ini');    // ini类型
$config->load(__DIR__ . '/config/test.xml', 'xml');    // xml类型
// $config->load(__DIR__ . '/config/test.yaml');    // yaml类型
$config->load(__DIR__ . '/../composer.json', 'json');    // json类型

$res = $config->get();


var_dump($res);