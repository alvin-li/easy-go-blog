<?php

/**
 * 数据库类(PDO)
 */

namespace Core;

use \Exception;
use \PDO;
use Core\Config;
use Core\Log;

class Db
{

	//数据库连接句柄
	protected $dbh;
	//数据库配置
	private $dbConf;
	//debug
	public $debug = false;
    //debug file resource
    private $debugFileResource;

	/**
	 * __construct
	 *
	 * @desc 构造器
	 * @access private
	 * @return void
	 */
	public function __construct($dbConfKey='', $debug=false)
	{
		if (!empty($dbConfKey)) {
			$this->dbConf = Config::getConfByKey('database', $dbConfKey);
			if (!empty($this->dbConf)) {
				$this->connectDb($this->dbConf);
			}
		}
        $this->debug = $debug;
        if (true===$this->debug) {
            $this->debugFileResource = new Log(DB_DEBUG_FILE, 'd');
            $this->debugFileResource->setCommonMsg(array('type'=>'sql_debug'));
        }
	}

	/**
	 * 连接数据库
	 * @param type $config
	 * @throws PDOException
	 * @return void
	 */
	public function connectDb($config)
	{
		$dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['dbname'];
		$options = array(
            PDO::ATTR_TIMEOUT => $config['timeout'],
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
        $this->dbh = new PDO($dsn, $config['username'], $config['pwd'], $options);
	}

	/**
	 * getConnection
	 *
	 * @desc 取得数据库连接句柄
	 * @access public
	 * @return object
	 */
	public function getConnection()
	{
		return $this->dbh;
	}

	/**
	 * getAll
	 *
	 * @desc 查询多条数据
	 * @param string $sql SQL语句
	 * @throws Exception
	 * @access public
	 * @return array 二维数组
	 */
	public function getAll($sql, $data=array())
	{
        $statement = $this->query($sql, $data);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * getOne
	 *
	 * @desc 查询一条数据
	 * @param string $sql SQL语句
	 * @throws Exception
	 * @access public
	 * @return array 一维数组
	 */
	public function getOne($sql, $data=array())
	{
        $statement = $this->query($sql, $data);
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * insert
	 *
	 * @desc 写入数据
	 * @param string $sql
	 * @throws Exception
	 * @access public
	 * @return int
	 */
	public function insert($table, $data)
	{
        $sql = $this->getInsertSql($table, $data);
		$this->exec($sql);
		return $this->dbh->lastInsertId();
	}

	/**
	 * execute
	 *
	 * @desc 执行 UPDATE | DELETE 操作
	 * @param string $sql
	 * @throws Exception
	 * @access public
	 * @return int
	 */
	public function execute($sql)
	{
		return $this->exec($sql);
	}

	/**
	 * count
	 *
	 * @desc 统计行数
	 * @param string $sql
	 * @throws Exception
	 * @access public
	 * @return int
	 */
	public function count($sql)
	{
        $statement = $this->query($sql);
		return $statement->fetchColumn();
	}

	/**
	 * 条件数组拼接为prepare()参数sql
	 * 会对参数进行转义
	 * @param array $where 条件数组
	 * @return string
	 */
	public function getWhereSql(array $where)
	{
		$whereSql = '';
		if (!empty($where) && is_array($where)) {
			foreach ($where as $key => $value) {
                if (is_numeric($value)) {
                    $whereSql .= preg_match("/>|</i", $key) ? ($key.$value) : ($key.'='.$value);
                    $whereSql .= ' and ';
                    continue;
                }
                $quoteStr = $this->dbh->quote($value);
                $whereSql .= preg_match("/>|</i", $key) ? ($key.$quoteStr) : ($key.'='.$quoteStr);
                $whereSql .= ' and ';
			}
            $whereSql = substr_replace($whereSql, '', -5, 5);
		}
		return $whereSql;
	}

	/**
	 * 拼接insert SQL
	 * @param string $table 表名
	 * @param array $data 需要添加的数据
	 * @retun string
	 */
	public function getInsertSql($table, $data)
	{
		$sql = 'insert into '.$table.'(`' . implode('`,`', array_keys($data)) . '`) values(';
        foreach ($data as $value) {
            $sql .= $this->dbh->quote($value) . ',';
        }
        $sql = rtrim($sql, ',');
        $sql .= ')';
        return $sql;
	}

	/**
	 * 执行查询sql
	 * @param string $sql SQL语句
	 * @throws Exception
     * @return object PDOStatement Object
	 */
	private function query($sql)
	{
        try {
            if (true===$this->debug) {
                $this->debugFileResource->debugLog(array('sql'=>$sql));
            }
            $statementObj = $this->dbh->prepare($sql);
            if (false===$statementObj) {
                $exceptionInfo = $this->dbh->errorInfo();
                throw new \Exception($exceptionInfo[2], $exceptionInfo[1]);
            }
            $statementObj->execute();
        } catch (Exception $exc) {
            throw $exc;
        }
		return $statementObj;
	}

	/**
	 * 执行sql
	 * @param stirng $sql
	 * @return mixed
	 * @throws Exception
	 */
	private function exec($sql)
	{
        if (true===$this->debug) {
            $this->debugFileResource->debugLog(array('sql'=>$sql));
        }
		$result = $this->dbh->exec($sql);
		if (false===$result) {
			$exceptionInfo = $this->dbh->errorInfo();
			throw new Exception($exceptionInfo[2], $exceptionInfo[1]);
		}
		return $result;
	}

	/**
	 * __destruct
	 *
	 * @desc 释放数据库连接句柄
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		$this->dbh = null;
        if (is_resource($this->debugFileResource)) {
            $this->debugFileResource->closeFile();
        }
	}
}
