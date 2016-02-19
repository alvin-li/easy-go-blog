<?php

/* 
 * 项目入口
 */

define('ROOT_PATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);  //项目根目录

require_once 'config/init.conf.php';
require_once CORE_PATH.'Loader.php';

//捕获错误信息
function getLastErr()
{
    $errInfo = error_get_last();
    if (null!=$errInfo) {
        $fileName = ROOT_PATH . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
        $errStr = date('Y-m-d H:i:s');
        foreach ($errInfo as $key => $value) {
            $errStr .= $key . '=' . $value . ',';
        }
        file_put_contents($fileName, $errStr, FILE_APPEND);
    }
}
register_shutdown_function('getLastErr');

registerAutoLoad();
try {
    $routerObj = new Core\Router();
    $routerObj->dispatch();
} catch (Exception $exc) {
    $logObj = new Core\Log(EXCEPTION_LOG_FILE, false, EXCEPTION_LOG_PATH);
    $logObj->exceptionLog(array('code'=>$exc->getCode(), 'msg'=>$exc->getMessage(), 'file'=>$exc->getFile(), 'trace'=>$exc->getTraceAsString()));
    $logObj->closeFile();
    if ('dev'==ENV) {
        Core\CommonFunction::show_exception($exc);
    }
}
