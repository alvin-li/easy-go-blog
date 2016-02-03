<?php

/* 
 * 项目入口
 */

define('ROOT_PATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);  //项目根目录

require_once 'config/init.conf.php';
require_once CORE_PATH.'Loader.php';

registerAutoLoad();
try {
    $routerObj = new Core\Router();
    $routerObj->dispatch();
} catch (Exception $exc) {
    Core\CommonFunction::show_exception($exc);
}
