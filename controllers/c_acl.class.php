<?php
!defined('IN_FRAME') && die('404 Page');
/* TODO:所有的操作都未加入权限判断(有空再做吧) */
class c_acl extends Controller {
    
    /* 模型 */
    private $mdl;
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->mdl = Helper::loadModel('acl');
    }
    
    /**
     * 删除数据
     */
    public function del() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->mdl->update(array('rec_status' => '0'), $id);
            $this->mdl->freshData();
        }
        Helper::jsLoadTo(HTTP_URL . '/?c=acl');
    }
    
    /**
     * 清除数据
     */
    public function clean() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->mdl->delete($id);
            $this->mdl->freshData();
        }
        Helper::jsLoadTo(HTTP_URL . '/?c=acl');
    }
    
    /**
     * 还原已删除的数据
     */
    public function back() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->mdl->update(array('rec_status' => '1'), $id);
        }
        Helper::jsLoadTo(HTTP_URL . '/?c=acl');
    }
    
    /**
     * 编辑页
     */
    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $arrData = null;
        $id > 0 && $arrData = $this->mdl->getOne($id);
        if ($arrData) {
            if (isset($_POST['sub_btn_0'])) {
                unset($_POST['sub_btn_0']);
                $blnOk = false;
                $strMsg = '';
                if (!empty($_POST['name']) && !empty($_POST['name_en'])) {
                    $arrExist = $this->mdl->getList('', -1);
                    if ($_POST['name_en'] != $arrData['name_en'] && isset($arrExist[$_POST['name_en']])) {
                        $strMsg = '元素已存在';
                    } else {
                        $blnOk = $this->mdl->update($_POST, array('id' => $id));
                        $this->mdl->freshData();
                    }
                }
                if (empty($strMsg)) {
                    $strMsg = $blnOk ? '修改成功' : '中/英文名称不能为空';
                }
                Helper::jsLoadTo(HTTP_URL . '/?c=acl&a=edit&id=' . $id, $strMsg);
            } elseif (isset($_POST['sub_btn'])) {
                $arrCheck = array('id' => array(), 'name' => array());
                if (isset($_POST['checked_values']) && is_array($_POST['checked_values'])) {
                    foreach ($_POST['checked_values'] as $str) {
                        $arrTemp = explode('|', $str);
                        array_push($arrCheck['id'], $arrTemp[0] . '_' . $arrTemp[1]);
                        array_push($arrCheck['name'], $arrTemp[2] . '|' . $arrTemp[3]);
                    }
                }
                $arrCheck = json_encode($arrCheck);
                $arrCheck = array('data' => $arrCheck);
                $this->mdl->update($arrCheck, array('id' => $id));
                $this->mdl->freshData();
                Helper::jsLoadTo(HTTP_URL . '/?c=acl&a=edit&id=' . $id, '保存设置成功');
            }
            $arrAssign = array(
                'id' => $id,
                'strTitle' => 'ACL - edit',
                'arrData' => $arrData,
                'arrProject' => array(),
                'arrAction' => array(),
                'strCheck' => ''
            );
            if ('g' == $arrData['type']) {
                $arrAssign['arrProject'] = $this->mdl->getProjects();
                $arrAssign['arrAction'] = $this->mdl->getActions();
                $arrTemp = json_decode($arrData['data'], true);
                if ($arrTemp && isset($arrTemp['id']) && count($arrTemp['id'] > 0)) {
                    $arrAssign['strCheck'] = '#' . implode(', #', $arrTemp['id']);
                }
            }
            $this->_display('acl_edit.php', $arrAssign);
        } else {
            Helper::jsLoadTo(HTTP_URL . '/?c=acl', '参数错误');
        }
    }
    
    /**
     * 首页
     */
    public function index() {
        if (isset($_POST['sub_btn'])) {
            unset($_POST['sub_btn']);
            $blnOk = false;
            $strMsg = '';
            if (!empty($_POST['name']) && !empty($_POST['name_en'])) {
                $arrExist = $this->mdl->getList($_POST['type'], -1);
                if (isset($arrExist[$_POST['name_en']])) {
                    $strMsg = '元素已存在';
                } else {
                    $blnOk = $this->mdl->insert($_POST);
                }
            }
            if (empty($strMsg)) {
                $strMsg = $blnOk ? '添加成功' : '中/英文名称不能为空';
            }
            Helper::jsLoadTo(HTTP_URL . '/?c=acl', $strMsg);
        }
        $arrData = array(
            'arrType' => $this->mdl->arrType,
            'strTitle' => 'ACL - index',
            'arrListG' => $this->mdl->getGroups(),
            'arrListP' => $this->mdl->getProjects(),
            'arrListA' => $this->mdl->getActions(),
            'arrList_' => $this->mdl->getList('', 0)
        );
        $this->_display('acl_index.php', $arrData);
    }
    
    /**
     * 测试
     */
    public function check() {
        $bln = $this->mdl->checkPermission(100, 2, 6);
        var_dump($bln);
        $bln = $this->mdl->checkPermission(99, 2, 6);
        var_dump($bln);
        $bln = $this->mdl->checkPermission(1, 2, 6);
        var_dump($bln);
    }
    
}