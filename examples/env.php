<?php

use mon\env\Env;

require_once __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/.env');

var_dump(Env::get());
var_dump(Env::get('AA'));
var_dump(Env::get('AB', 'test'));