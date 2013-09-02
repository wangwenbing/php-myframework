<?php
!defined('IN_FRAME') && die('404 Page');
abstract class Model {
    
    public $db = null;
    public $table = null;
    
    public function __construct() {
        if ($this->table) {
            $this->db = Db::getInstance();
        }
    }
    
    public function query($strSql) {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->query($strSql);
        }
        return $re;
    }
    
    public function count($arrWhere) {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->count($this->table, $arrWhere);
        }
        return $re;
    }
    
    public function insert($arrData) {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->insert($this->table, $arrData);
        }
        return $re;
    }
    
    public function getOne($arrWhere, $arrWhich = '*', $strExt = 'LIMIT 0, 1') {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->getOne($this->table, $arrWhere, $arrWhich, $strExt);
        }
        return $re;
    }
    
    public function select($arrWhere, $arrWhich = '*', $strExt = '') {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->select($this->table, $arrWhere, $arrWhich, $strExt);
        }
        return $re;
    }
    
    public function update($arrSet, $arrWhere) {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->update($this->table, $arrSet, $arrWhere);
        }
        return $re;
    }
    
    public function delete($arrWhere) {
        $re = false;
        if ($this->db && $this->table) {
            $re = $this->db->delete($this->table, $arrWhere);
        }
        return $re;
    }
}