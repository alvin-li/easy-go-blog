<?php

/**
 * 数据库类(PDO)
 */

namespace Core;

use \Exception;
use \PDO;
use Core\Config;

class DbRetry
{

    //数据库连接句柄
    protected static $dbh;
    //数据库配置
    private $dbConf;
    //debug
    public $debug = false;
    //sql执行失败后,是否重试
    private $isRetry = false;
    //sql执行失败后的重试次数
    private $retryNum = 3;
    //sql执行失败后重试的间隔时间,单位:秒
    private $retrySleep = 1;

    /**
     * __construct
     *
     * @desc 构造器
     * @access private
     * @return void
     */
    public function __construct($dbConfKey = '', $isRetry=false, $retryNum=3, $retrySleep=1)
    {
        if (!empty($dbConfKey)) {
            $this->dbConf = Config::getConfByKey('database', $dbConfKey);
            if (!empty($this->dbConf)) {
                $this->retryFun('connectDb', $this->dbConf);
            }
        }
        $this->isRetry = (boolean)$isRetry;
        $this->retryNum = (int)$retryNum;
        $this->retrySleep = (int)$retrySleep;
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

    private function retryFun($functionName, $params)
    {
        $i = 0;
        while ($i<$this->retryNum) {
            try {
                $this->$functionName($params);
                break;
            } catch (Exception $exc) {
                $i++;
                if ($i==$this->retryNum) {
                    throw $exc;
                }
                if ($this->isRetry) {
                    sleep($this->retrySleep);
                } else {
                    throw $exc;
                }
            }
        }
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
     * @desc 查询数据
     * @param string $sql
     * @throws Exception
     * @access public
     * @return array
     */
    public function getAll($sql)
    {
        $statment = $this->query($sql);
        return $statment->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getOne
     *
     * @desc 只查询一条数据
     * @param string $sql
     * @throws Exception
     * @access public
     * @return array
     */
    public function getOne($sql)
    {
        $statment = $this->query($sql);
        return $statment->fetch(PDO::FETCH_ASSOC);
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
    public function insert($sql)
    {
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
        $statment = $this->query($sql);
        return $statment->fetchColumn();
    }

    /**
     * 条件数组拼接为sql
     *
     * @param type $where 条件数组
     * @return string
     */
    public function whereSql(&$where)
    {
        $whereSql = '';
        if (!empty($where) && is_array($where)) {
            $whereSql = ' where ';
            foreach ($where as $key => $value) {
                if (preg_match("/>|</i", $key)) {
                    $whereSql .= $key . $this->dbh->quote($value) . " and ";
                } else {
                    $whereSql .= $key . "=" . $this->dbh->quote($value) . " and ";
                }
            }
            $whereSql = rtrim($whereSql, ' and ');
        }
        return $whereSql;
    }

    /**
     * 执行查询sql
     * @param string $sql
     * @throws Exception
     */
    private function query($sql)
    {
        $statment = $this->dbh->query($sql);
        if ($statment === FALSE) {
            $exceptionInfo = $this->dbh->errorInfo();
            throw new \Exception($exceptionInfo[2], $exceptionInfo[1]);
        }
        return $statment;
    }

    /**
     * 执行sql
     * @param stirng $sql
     * @return mixed
     * @throws Exception
     */
    private function exec($sql)
    {
        $result = $this->dbh->exec($sql);
        if ($result === FALSE) {
            $exceptionInfo = $this->dbh->errorInfo();
            throw new \Exception($exceptionInfo[2], $exceptionInfo[1]);
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
    }
}
