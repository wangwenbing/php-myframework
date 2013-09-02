<?php
/* The default controller */
!defined('IN_FRAME') && die('404 Page'); // 用来强制单一入口,建议添加
class c_index extends Controller {
    // Add your public function(s) below...
    
    /**
     * 一个测试
     * 请访问 HTTP_URL/?c=index&a=test
     */
    public function test() {
        $this->_cache(2); // 页面缓存2秒
        $arrAssign = array(
            'now' => date('Y-m-d H:i:s')
        );
        $this->_display('test.php', $arrAssign); // 模板显示方法
    }
    
    public function test1() {
        $this->_cache(2); // 未使用模板做页面输出缓存
        echo date('Y-m-d H:i:s');
    }
}