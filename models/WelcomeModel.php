<?php

namespace Models;

use Core\Db;

class WelcomeModel extends Db
{
    //table name
    private $table = 'test';

    public function __construct()
    {
        parent::__construct('test', true);
    }

    /**
     * @param array $where 查询条件
     * @param string $column 查询的字段
     * @throw Exception
     * @return array
     */
    public function getAllData($where, $column='*')
    {
        $sql = 'select '.$column.' from '.$this->table;
        if (!empty($where) && is_array($where)) {
            $sql .= ' where '.$this->getWhereSql($where);
        }
        return $this->getAll($sql, $where);
    }

    /**
     * @param array $data 添加的数据,key为字段,value为值
     * @return primary id
     */
    public function addData($data)
    {
        return $this->insert($this->table, $data);
    }
}
