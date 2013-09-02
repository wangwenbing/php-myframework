<?php
!defined('IN_FRAME') && die('404 Page');
class m_acl extends Model {
    
    /* 元素类型 */
    public $arrType = array(
        'g' => '用户组别',
        'p' => '操作对象',
        'a' => '操作类型'
    );
    
    private $strTemp = '';
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->table = 't_acl';
        parent::__construct();
        if (!in_array($this->table, $this->db->showTables())) {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->table}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` set('g','p','a') NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `data` longtext,
  `rec_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `name_en` (`name_en`)
) ENGINE=MyISAM
SQL;
            $this->query($sql);
            $this->insertInitData();
        }
    }
    
    /**
     * 插入初始数据
     */
    private function insertInitData() {
        $arrData = array(
            array(
                'id' => 1,
                'type' => 'g',
                'name' => '超级管理员',
                'name_en' => 'superadmin',
                'title' => '超级管理员,可以掌控一切',
                'data' => '{"id":["2_3","2_4","2_5","2_6"],"name":["acl|view","acl|add","acl|update","acl|delete"]}'
            ),
            array(
                'id' => 2,
                'type' => 'p',
                'name' => '权限控制',
                'name_en' => 'acl',
                'title' => '用户的权限控制'
            ),
            array(
                'id' => 3,
                'type' => 'a',
                'name' => '查看',
                'name_en' => 'view',
                'title' => '查看数据'
            ),
            array(
                'id' => 4,
                'type' => 'a',
                'name' => '添加',
                'name_en' => 'add',
                'title' => '添加数据'
            ),
            array(
                'id' => 5,
                'type' => 'a',
                'name' => '修改',
                'name_en' => 'update',
                'title' => '修改数据'
            ),
            array(
                'id' => 6,
                'type' => 'a',
                'name' => '删除',
                'name_en' => 'delete',
                'title' => '删除数据'
            )
        );
        foreach ($arrData as $arr) {
            $this->insert($arr);
        }
    }
    
    /**
     * 取列表
     */
    public function getList($strType = '', $intRecStatus = 1, $blnIdAsKey = false) {
        $arrWhere = array();
        in_array($intRecStatus, array(0, 1)) && $arrWhere['rec_status'] = $intRecStatus;
        isset($this->arrType[$strType]) && $arrWhere['type'] = $strType;
        $arrTemp = $this->select($arrWhere, '*', 'ORDER BY `type`');
        $re = array();
        foreach ($arrTemp as $arr) {
            $strKey = $blnIdAsKey ? '_' . $arr['id'] : $arr['name_en'];
            $re[$strKey] = $arr;
        }
        unset($arrTemp);
        return $re;
    }
    
    /**
     * 取所有的组
     */
    public function getGroups() {
        return $this->getList('g');
    }
    
    /**
     * 取所有的对象
     */
    public function getProjects() {
        return $this->getList('p');
    }
    
    /**
     * 取所有的操作
     */
    public function getActions() {
        return $this->getList('a');
    }
    
    /**
     * 设置文件缓存
     */
    private function setCacheStorage() {
        phpFastCache::$storage = 'files';
        phpFastCache::$securityKey = 'acl';
        phpFastCache::$path = CACHES_PATH;
    }
    
    /**
     * 验证用户组操作对象的权限
     */
    public function checkPermission($group, $project, $action) {
        $blnRe = false;
        $strTemp = $group . '|' . $project . '|' . $action;
        $this->setCacheStorage();
        $data = phpFastCache::get($group);
        if (is_array($data) && count($data) > 0) {
            if (isset($data['rec_status']) && $data['rec_status'] == 1) {
                if (is_numeric($project) && is_numeric($action)) {
                    $blnRe = in_array($project . '_' . $action, $data['id']);
                } else {
                    $blnRe = in_array($project . '|' . $action, $data['name']);
                }
            }
        } elseif ($this->strTemp !== $strTemp) {
            $this->strTemp = $strTemp;
            $this->freshData();
            $blnRe = $this->checkPermission($group, $project, $action);
        }
        return $blnRe;
    }
    
    /**
     * 更新数据和缓存
     */
    public function freshData($id = null, $arrP = null, $arrA = null) {
        $this->setCacheStorage();
        is_null($arrP) && $arrP = $this->getList('p', 1, true);
        is_null($arrA) && $arrA = $this->getList('a', 1, true);
        if (is_null($id)) {
            phpFastCache::cleanup();
            $arrG = $this->getGroups();
            foreach ($arrG as $arr) {
                $this->freshData($arr['id'], $arrP, $arrA);
            }
        } else {
            $arrOne = $this->getOne(array('id' => $id, 'type' => 'g'));
            if ($arrOne) {
                $arrData = $arrOne['data'];
                $arrData = json_decode($arrData, true);
                if (is_array($arrData) && isset($arrData['id']) && count($arrData['id']) > 0) {
                    $arrData = $arrData['id'];
                    $arrNew = array('id' => array(), 'name' => array());
                    foreach ($arrData as $str) {
                        $arrTemp = explode('_', $str);
                        if (count($arrTemp) == 2 && isset($arrP['_' . $arrTemp[0]]) && isset($arrA['_' . $arrTemp[1]])) {
                            array_push($arrNew['id'], $str);
                            array_push($arrNew['name'], $arrP['_' . $arrTemp[0]]['name_en'] . '|' . $arrA['_' . $arrTemp[1]]['name_en']);
                        }
                    }
                    $this->update(array('data' => json_encode($arrNew)), array('id' => $id));
                    $arrNew['rec_status'] = $arrOne['rec_status'];
                    phpFastCache::set($id, $arrNew, 864000);
                    phpFastCache::set($arrOne['name_en'], $arrNew, 864000);
                }
            }
        }
    }
}