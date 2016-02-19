<?php

namespace Core;

/**
 * 日志类
 *
 * @author lipengfei
 */

class Log
{

	// log file name
	private $logFileName;
	// a file pointer resource
	private $fp;
	// 记录日志时的通用信息，每次调用saveLogs时都会记录此属性
	public $commonMsg;
	//separator
    private $separator = ',';
	//log level,default DEBUG
	private $logLevel = 'DEBUG';

	/**
	 * init class
	 * @param string $logFileName 	文件名称,不包括后缀
	 * @param bool $roateByWhat 按什么切分，Y按年，m按月,d按天，默认false不切分
	 * @param string $logPath	文件路径
	 */
	public function __construct($logFileName, $roateByWhat = FALSE, $logPath = LOGS_PATH)
	{
		//create log path
		if (!file_exists($logPath)) {
			mkdir($logPath, 0644, true);
		}

		$this->logFileName = $logPath . $logFileName;
		$this->getRoateLogFileName($roateByWhat);
		$this->fp = fopen($this->logFileName, 'a+');
	}

    /**
     * 设置通用日志信息
     * @param array $commonMsg
     */
    public function setCommonMsg($commonMsg)
    {
        $this->commonMsg = $commonMsg;
    }

    /**
     * 记录debug级别日志
     * @param array $logArr 日志内容
     * @return void
     */
    public function debugLog($logArr)
    {
        $this->setLogLevel('DEBUG');
        $this->saveLogs($logArr);
    }

    /**
     * 记录info级别日志
     * @param array $logArr 日志内容
     * @return void
     */
	public function infoLog($logArr)
	{
		$this->setLogLevel('INFO');
        $this->saveLogs($logArr);
	}

    /**
     * 记录warning级别日志
     * @param array $logArr 日志内容
     * @return void
     */
    public function warningLog($logArr)
    {
        $this->setLogLevel('WARNING');
        $this->saveLogs($logArr);
    }

    /**
     * 记录error级别日志
     * @param array $logArr 日志内容
     * @return void
     */
    public function errorLog($logArr)
    {
        $this->setLogLevel('ERROR');
        $this->saveLogs($logArr);
    }

    /**
     * 记录exception级别日志
     * @param array $logArr 日志内容
     * @return void
     */
    public function exceptionLog($logArr)
    {
        $this->setLogLevel('EXCEPTION');
        $this->saveLogs($logArr);
    }

	/**
	 * set log level
	 * @param string $levelStr
	 */
	public function setLogLevel($levelStr)
	{
		$this->logLevel = strtoupper($levelStr);
	}

	/**
	 * 获取切分日志的文件名
	 * @param string $roateByWhat 默认不切分
	 */
	private function getRoateLogFileName($roateByWhat)
	{
		$result = '_';
		switch ($roateByWhat) {
			case 'Y':
				$result .= date('Y');
				break;

			case 'm':
				$result .= date('Ym');
				break;
			case 'd':
				$result .= date('Ymd');
				break;
			default:
				$result = '';
				break;
		}
        $this->logFileName .= $result . '.log';
	}

	/**
	 * Saves log messages in files
	 * @param array $msg 日志内容
	 * @return void
	 */
	private function saveLogs($msg)
	{
        fwrite($this->fp, $this->formatLogMessage($msg));
	}

	/**
	 * close file resource
	 */
	public function closeFile()
	{
		if (is_resource($this->fp)) {
			fclose($this->fp);
		}
	}

	/**
	 * 格式化日志内容
	 * @param array $msg 日志内容数组,key为数字的则不记录key只记录value
	 * @return string 末尾带有换行符的字符串
	 */
	protected function formatLogMessage($msg)
	{
		$content = '['.$this->logLevel.']'.'datetime='.date('Y-m-d H:i:s') . $this->separator;
		if (!empty($this->commonMsg) && is_array($this->commonMsg)) {
			$msg = array_merge($this->commonMsg, $msg);
		}
		foreach ($msg as $key => $value) {
			if (is_string($key)) {
				$content .= $key . '=' . $value . $this->separator;
			} else {
				$content .= $value . $this->separator;
			}
		}
		$content .= "\n";
		return $content;
	}

	/**
	 * 写日志
	 * @param string $logFileName 文件名称
	 * @param array $msg 日志信息
	 * @return void
	 */
	public static function writeLog($logFileName, $msg)
	{
		$filePath = dirname($logFileName);
		if (!file_exists($filePath)) {
			mkdir($filePath, 0644, true);
		}
		$content = date('Y-m-d H:i:s') . ' ';
		foreach ($msg as $key => $value) {
			if (is_string($key)) {
				$content .= $key . '=' . $value . ',';
			} else {
				$content .= $value . ',';
			}
		}
		$content .= "\n";
		file_put_contents($logFileName, $content, FILE_APPEND);
	}
}
