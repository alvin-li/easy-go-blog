<?php

namespace Core;

/* 
 * 
 */

class Controller
{

	/**
	 * 获取GET参数
	 * @param string $key 需要获取的参数,不传key则默认获取所有GET参数
	 * @return array/string
	 */
	public function getParam($key='')
	{
		$result = '';
		if (''===$key) {
			$result = $_GET;
		} elseif (isset($_GET[$key])) {
			$result = $_GET[$key];
		}
		return $result;
	}

	/**
	 * 获取POST参数
	 * @param string $key 需要获取的参数,不传key则默认获取所有POST参数
	 * @return array/string
	 */
	public function postParam($key='')
	{
		$result = '';
		if (''===$key) {
			$result = $_POST;
		} elseif (isset($_POST[$key])) {
			$result = $_POST[$key];
		}
		return $result;
	}
}