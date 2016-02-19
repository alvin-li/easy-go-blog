<?php

/**
 * 常用函数
 */

namespace Core;

class CommonFunction
{
    /**
     * 显示异常页面
     * @param object $exceptionObj Exception Object
     */
    public static function show_exception($exceptionObj)
    {
        require VIEW_PATH.'error/exception.html';
    }
}
