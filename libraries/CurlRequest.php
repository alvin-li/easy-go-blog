<?php

namespace Libraries;

use \Exception;
use Core\Log;

/**
 * Class CurlRequest
 * 远程请求类
 * @package Core
 */

class CurlRequest
{
    //默认配置
    public $defaultOptions = array(
        CURLOPT_TIMEOUT        => 1,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    );

    //重试次数
    public $retryNum = 0;
    
    //debug
    public $debug = false;
    private $logObj;

    public function __construct($debug=false)
    {
        $this->debug = $debug;
        if (true===$this->debug) {
            $this->logObj = new Log('curl');
        }
    }

    /**
     * 设置curl的options参数
     * @param resource &$ch     curl_init返回的resource
     * @param array    $options 详细参数
     */
    private function setOptions(&$ch, array $options)
    {
        $allOptions = array_merge($this->defaultOptions, $options);
        curl_setopt_array($ch, $allOptions);
    }

    /**
     * 模拟http GET请求
     * @param  string $url       请求地址
     * @param  array  $paramsArr GET参数
     * @param  array  $options   curl参数
     * @return mixed
     */
    public function get($url, array $paramsArr = array(), array $options = array())
    {
        if (empty($url)) {
            return false;
        }
        $resultUrl = $url . "?" . http_build_query($paramsArr);
        $options[CURLOPT_URL] = $resultUrl;
        return $this->exec($options);
    }

    /**
     * 模拟http POST请求
     * @param  string $url       请求地址
     * @param  array  $paramsArr POST参数
     * @param  array  $options   curl 参数
     * @return mixed
     */
    public function post($url, array $paramsArr, array $options = array())
    {
        if (empty($url)) {
            return false;
        }
        $postParams = http_build_query($paramsArr);
        $options[CURLOPT_POST]       = true;
        $options[CURLOPT_POSTFIELDS] = $postParams;
        $options[CURLOPT_URL]        = $url;
        return $this->exec($options);
    }

    /**
     * 实现HTTP请求
     * @param  array $options curl options参数
     * @return mixed
     */
    private function exec(array $options)
    {
        $ch = curl_init();
        $this->setOptions($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
        if (false===$response) {
            $log_params = array(
                'errmsg'       => 'curl_get_reqest_error',
                'curl_err_msg' => curl_error($ch),
                'curl_errcode' => curl_errno($ch),
                'url'          => $url
            );
            return false;
        }
        return $response;
    }
}