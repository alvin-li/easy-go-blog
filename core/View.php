<?php

namespace Core;

class View
{
    /**
     * @param string $path 模板路径,只需views/之后的部分,不包括文件后缀名
     * @param array $data 模板中需要使用的数据
     * @return void
     */
    public static function render($path, $data=array())
    {
        if (!empty($data) && is_array($data)) {
            extract($data, EXTR_REFS | EXTR_OVERWRITE);
        }
        require VIEW_PATH.$path.'.php';
    }
}