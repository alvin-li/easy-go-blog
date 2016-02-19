<?php

/* 
 * 初始化配置文件
 */

//环境配置
define('ENV', 'dev');//线下
//define('ENV', 'pro');//线上

//报错配置
if (ENV=='pro') {
    error_reporting(E_ERROR  | E_PARSE);
} else {
    error_reporting(E_ALL);
}

//路径配置
define('MODELS_PATH', ROOT_PATH.'models/');			//数据模型类目录
define('MODULE_PATH', ROOT_PATH.'modules/');		//逻辑计算模块目录
define('CORE_PATH', ROOT_PATH.'core/');				//核心类
define('CONF_PRO_PATH', ROOT_PATH.'config/');       //线上配置文件路径
define('CONF_DEV_PATH', ROOT_PATH.'config/dev/');   //开发配置文件路径
define('VIEW_PATH', ROOT_PATH.'views/');            //视图层根目录
define('LOGS_PATH', ROOT_PATH.'logs/');             //日志根目录

//异常常量定义
require_once CONF_PRO_PATH.'exception_constants.php';
require_once CONF_PRO_PATH.'log_constants.php';