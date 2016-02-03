<?php

function registerAutoLoad()
{
	spl_autoload_register('loadClass');
}
	
function loadClass($className)
{
	$className = str_replace('\\', DIRECTORY_SEPARATOR, lcfirst($className));
	$fileName = ROOT_PATH . $className . '.php';
	if (file_exists($fileName)) {
		require_once $fileName;
	}
}
