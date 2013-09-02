<?php
/* --- 若无特别需求,请勿修改此文件,谢谢 --- */
/**
 * 单例连接Mysql
 */
!defined('IN_FRAME') && die('404 Page');
class Db {

    /**
     * 类的属性
     */
    private $_conn = null;
    private $_db = null;
    private $_defaultDb = null;
    private static $_instance = null;
    
    /**
     * 私有的构造函数
     */
    private function __construct() {
        $strHost = 'localhost';
        if (defined('DB_HOST') && '' != DB_HOST) {
            $strHost = DB_HOST;
        }
        $strUser = 'root';
        if (defined('DB_USER') && '' != DB_USER) {
            $strUser = DB_USER;
        }
        $strPasd = 'root';
        if (defined('DB_PASD')) {
            $strPasd = DB_PASD;
        }
        $strSchema = 'mysql';
        if (defined('DB_BASE') && '' != DB_BASE) {
            $strSchema = DB_BASE;
        }
        $intPort = 3306;
        if (defined('DB_PORT') && DB_PORT > 0) {
            $intPort = DB_PORT;
        }
        $_conn = new mysqli($strHost, $strUser, $strPasd, $strSchema, $intPort);
        if (mysqli_connect_errno()) {
            die('Error: Can not connect to Database server!!!');
        } else {
            $this->_conn = $_conn;
            $this->_db = $strSchema;
            $this->_defaultDb = $strSchema;
            if (defined('DB_CHARSET') && '' != DB_CHARSET && defined('DB_COLLATE') && '' != DB_COLLATE) {
                $this->query('SET NAMES "' . DB_CHARSET . '" COLLATE "' . DB_COLLATE . '"');
            }
            register_shutdown_function(array(__CLASS__, 'close'));
        }
    }
    
    /**
     * 取单例对象
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 关闭链接
     */
    public static function close() {
        $re = false;
        if (!is_null(self::$_instance)) {
            $_this = self::$_instance;
            if ($_this->_conn) {
                $re = $_this->_conn->close();
            }
        }
        return $re;
    }
    
    /**
     * 查询
     */
    public function query($strSql) {
        // var_dump($strSql);
        $re = false;
        if ($this->_conn && !empty($strSql)) {
            $re = $this->_conn->query($strSql);
        }
        return $re;
    }
    
    /**
     * 多条查询
     */
    public function multiQuery($arrSql) {
        $re = false;
        if ($this->_conn) {
            if (!is_array($arrSql)) {
                $re = $this->query($arrSql);
            } elseif (count($arrSql) == 1) {
                $re = $this->query(current($arrSql));
            } else {
                $re = $this->_conn->multi_query(implode('; ', $arrSql));
            }
        }
        return $re;
    }
    
    /**
     * 只取一条数据
     */
    public function getOne($strTable, $arrWhere = null, $arrWhich = '*', $strExt = 'LIMIT 0, 1') {
        if (!is_array($arrWhere) && is_numeric($arrWhere)) {
            $arrWhere = array('id' => $arrWhere);
        }
        $one = false;
        $re = $this->select($strTable, $arrWhere, $arrWhich, $strExt);
        if (is_array($re) && count($re) > 0) {
            $one = $re[0];
        }
        return $one;
    }
    
    /**
     * 计算结果个数
     */
    public function count($strTable, $arrWhere = null) {
        $re = false;
        if ($this->_conn) {
            $strSql = 'SELECT COUNT(`id`) AS `cnt` FROM `' . $strTable . '`';
            if ($arrWhere) {
                $strSql .= ' WHERE ';
                $strSql .= is_array($arrWhere) ? $this->genWhereOrSetSql($arrWhere, ' AND ') : $arrWhere;
            }
            $result = $this->query($strSql);
            $arrRe = $this->fetchResult($result);
            if (is_array($arrRe) && isset($arrRe[0]) && isset($arrRe[0]['cnt']) && is_numeric($arrRe[0]['cnt'])) {
                $re = $arrRe[0]['cnt'];
            }
        }
        return $re;
    }
    
    /**
     * 查询数据
     */
    public function select($strTable, $arrWhere = null, $arrWhich = '*', $strExt = '') {
        $re = false;
        if ($this->_conn) {
            $strSql = 'SELECT ';
            if (is_array($arrWhich)) {
                $blnFirst = true;
                foreach ($arrWhich as $v) {
                    $blnFirst ? $blnFirst = false : $strSql .= ', ';
                    $strSql .= '`' . $v . '`';
                }
            } else {
                $strSql .= $arrWhich;
            }
            $strSql .= ' FROM `' . $strTable . '`';
            if ($arrWhere) {
                $strSql .= ' WHERE ';
                $strSql .= is_array($arrWhere) ? $this->genWhereOrSetSql($arrWhere, ' AND ') : $arrWhere;
            }
            !empty($strExt) && $strSql .= ' ' . $strExt;
            $result = $this->query($strSql);
            $re = $this->fetchResult($result);
        }
        return $re;
    }
    
    /**
     * 插入数据 
     */
    public function insert($strTable, $arrData) {
        $re = false;
        if ($this->_conn && is_array($arrData) && count($arrData) > 0) {
            $strSql = 'INSERT INTO `' . $strTable . '` ';
            $strSqlA = '(';
            $strSqlB = '(';
            $blnFirst = true;
            foreach ($arrData as $k => $v) {
                if (!$blnFirst) {
                    $strSqlA .= ', ';
                    $strSqlB .= ', ';
                } else {
                    $blnFirst = false;
                }
                $strSqlA .= '`' . $k . '`';
                $strSqlB .= '"' . addslashes($v) . '"';
            }
            $strSql .= $strSqlA . ') VALUES ' . $strSqlB . ')';
            if ($this->query($strSql)) {
                $re = $this->getNewId();
            }
        }
        return $re;
    }
    
    /**
     * 插入多条数据(需要严谨的数组结构)
     */
    public function multiInsert($strTable, $arrData, $arrKey = null) {
        $re = false;
        if ($this->_conn && is_array($arrData) && count($arrData) > 0) {
            $strSql = 'INSERT INTO `' . $strTable . '` ';
            if (!is_array($arrKey) || count($arrKey) == 0) {
                $arrKey = array_keys(current($arrData));
            }
            $strSql .= '(`' . implode('`, `', $arrKey) . '`) VALUES ';
            $arrData = array_map(array(__CLASS__, 'genMultiInsertSql'), $arrData);
            $strSql .= implode(', ', $arrData);
            $re = $this->query($strSql);
        }
        return $re;
    }
    
    /**
     * 生成多条插入的SQL(array_map 之 callback)
     */
    private static function genMultiInsertSql($arr) {
        $re = '(';
        $blnFirst = true;
        foreach ($arr as $s) {
            $blnFirst ? $blnFirst = false : $re .= ', ';
            $re .= '"' . addslashes($s) . '"';
        }
        $re .= ')';
        return $re;
    }
    
    /**
     * 生成相关的SQL字符串
     */
    private function genWhereOrSetSql($arr, $chr = ', ') {
        $re = '';
        if ($this->_conn) {
            $blnFirst = true;
            foreach ($arr as $k => $v) {
                $blnFirst ? $blnFirst = false : $re .= $chr;
                $re .= '`' . $k . '` = "' . addslashes($v) . '"';
            }
            empty($re) && $re = '1 > 0';
        }
        return $re;
    }
    
    /**
     * 更新数据
     */
    public function update($strTable, $arrSet, $arrWhere = null) {
        $re = false;
        if (!is_array($arrWhere) && is_numeric($arrWhere)) {
            $arrWhere = array('id' => $arrWhere);
        }
        if ($this->_conn) {
            $strSql = 'UPDATE `' . $strTable . '` SET ';
            $strSql .= is_array($arrSet) ? $this->genWhereOrSetSql($arrSet) : $arrSet;
            if ($arrWhere) {
                $strSql .= ' WHERE ';
                $strSql .= is_array($arrWhere) ? $this->genWhereOrSetSql($arrWhere, ' AND ') : $arrWhere;
            }
            $re = $this->query($strSql);
        }
        return $re;
    }
    
    /**
     * 删除数据
     */
    public function delete($strTable, $arrWhere) {
        $re = false;
        if (!is_array($arrWhere) && is_numeric($arrWhere)) {
            $arrWhere = array('id' => $arrWhere);
        }
        if ($this->_conn) {
            $strSql = 'DELETE FROM `' . $strTable . '` WHERE ';
            $strSql .= is_array($arrWhere) ? $this->genWhereOrSetSql($arrWhere, ' AND ') : $arrWhere;
            $re = $this->query($strSql);
        }
        return $re;
    }
    
    /**
     * 取刚插入的新ID
     */
    public function getNewId() {
        $re = false;
        if ($this->_conn) {
            $re = $this->_conn->insert_id;
        }
        return $re;
    }
    
    /**
     * 取影响的行数
     */
    public function getAffectedRows() {
        $re = false;
        if ($this->_conn) {
            $re = $this->_conn->affected_rows;
        }
        return $re;
    }
    
    /**
     * 取结果数据
     */
    public function countResult($objResult) {
        $re = false;
        if ($objResult) {
            $re = $objResult->num_rows;
        }
        return $re;
    }
    
    /**
     * 取结果
     */
    public function fetchResult($objResult) {
        $re = false;
        if ($objResult) {
            $re = array();
            while (($row = $objResult->fetch_array(MYSQLI_ASSOC)) !== null) {
                array_push($re, $row);
            }
        }
        return $re;
    }
    
    /**
     * 列出当前库的所有表
     */
    public function showTables() {
        $re = false;
        if ($this->_conn) {
            $re = $this->query('SHOW TABLES');
            $re = $this->fetchResult($re);
        }
        if (is_array($re) && count($re) > 0) {
            $arrTemp = array();
            foreach ($re as $arr) {
                array_push($arrTemp, current($arr));
            }
            $re = $arrTemp;
            unset($arrTemp);
        }
        return $re;
    }
    
    /**
     * 设置是否自动提交
     */
    public function autocommit($bln = true) {
        $re = false;
        if ($this->_conn) {
            $re = $this->_conn->autocommit($bln);
        }
        return $re;
    }
    
    /**
     * 提交查询
     */
    public function commit() {
        $re = false;
        if ($this->_conn) {
            $re = $this->_conn->commit();
        }
        return $re;
    }
    
    /**
     * 回滚
     */
    public function rollback() {
        $re = false;
        if ($this->_conn) {
            $re = $this->_conn->rollback();
        }
        return $re;
    }
    
    /**
     * 选择数据库
     */
    public function selectDb($strSchema = null) {
        $re = false;
        if ($this->_conn) {
            if (is_null($strSchema)) {
                $strSchema = $this->_defaultDb;
            }
            if ($this->_db == $strSchema) {
                $re = true;
            } else {
                $re = $this->_conn->select_db($strSchema);
                if ($re) {
                    $this->_db = $strSchema;
                }
            }
        }
        return $re;
    }
}