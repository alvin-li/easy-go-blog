<?php

namespace Core;

/**
 * Description of Log
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
	public function __construct($logFileName, $roateByWhat = FALSE, $logPath = '/tmp/')
	{
		//create log path
		if (!file_exists($logPath)) {
			mkdir($logPath, 0777, true);
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

    public function debugLog($logArr)
    {
        $this->setLogLevel('DEBUG');
        $this->saveLogs($logArr);
    }

	public function infoLog($logArr)
	{
		$this->setLogLevel('INFO');
        $this->saveLogs($logArr);
	}

    public function warningLog($logArr)
    {
        $this->setLogLevel('WARNING');
        $this->saveLogs($logArr);
    }

    public function errorLog($logArr)
    {
        $this->setLogLevel('ERROR');
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
		$result = '';
		switch ($roateByWhat) {
			case 'Y':
				$result .= '_' . date('Y') . '.log';
				break;

			case 'm':
				$result .= '_' . date('Ym') . '.log';
				break;
			case 'd':
				$result .= '_' . date('Ymd') . '.log';
				break;
			default:
				$result .= '.log';
				break;
		}
        $this->logFileName .= $result;
	}

	/**
	 * Saves log messages in files
	 * @param array $msg 日志内容
	 * @return void
	 */
	private function saveLogs($msg)
	{
		if (is_resource($this->fp)) {
			fwrite($this->fp, $this->formatLogMessage($msg));
		}
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
	 * @param string $roateByWhat 按什么切分，Y按年，m按月,d按天，默认false不切分
	 * @param string $logPath
	 * @return void
	 */
	public static function writeLog($logFileName, $msg)
	{
		$filePath = dirname($logFileName);
		if (!file_exists($logFileName)) {
			mkdir($logFileName, 0777, true);
		}
		$logFile = $logPath . $logFileName;
		$content = date('Y-m-d H:i:s') . ' ';
		foreach ($msg as $key => $value) {
			if (is_string($key)) {
				$content .= $key . '=' . $value . ',';
			} else {
				$content .= $value . ',';
			}
		}
		$content .= "\n";
		file_put_contents($logFile, $content, FILE_APPEND);
	}

}
