<?php
/* --- 若无特别需求,请勿修改此文件,谢谢 --- */
/**
 * 自定义Session的存储方式
 */
!defined('IN_FRAME') && die('404 Page');
class SessionCache {
    private $intLifeSec = 3600;
    private $strType = 'files';
    /**
     * $strType = native | apc | memcache | files
     */
    public function __construct($strType = 'native') {
        if ('native' != $strType) { // 如果不用原生的SESSION
            $this->strType = $strType;
            $this->setCacheStorage();
            session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
        }
    	session_start();
    }
    
    private function setCacheStorage() {
        phpFastCache::$storage = $this->strType;
        if ('files' == $this->strType) {
            phpFastCache::$securityKey = 'session_cache';
            phpFastCache::$path = CACHES_PATH;
        }
    }
    
    public function read($id) {
        $this->setCacheStorage();
        return phpFastCache::get('session_' . $id);
    }
    
    public function write($id, $data) {
        $this->setCacheStorage();
        return phpFastCache::set('session_' . $id, $data, $this->intLifeSec);
    }
    
    public function destroy($id) {
        $this->setCacheStorage();
        return phpFastCache::delete('session_' . $id);
    }
    
    public function open() {
        return true;
    }
    
    public function close() {
        return true;
    }
    
    public function gc() {
        return true;
    }
}