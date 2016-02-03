<?php

/**
 * 常用函数
 */

namespace Core;

class CommonFunction
{
    public static function show_exception($exceptionObj)
    {
        require VIEW_PATH.'error/exception.html';
    }
}
