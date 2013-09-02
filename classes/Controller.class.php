<?php
/**
 * 控制器的基类,可以按需求定制
 */
!defined('IN_FRAME') && die('404 Page');
abstract class Controller {
    
    private $_cache_seconds = 0; // 缓存的时间长度
    private $_cache_dir = 'pages';
    private $_cache_key = ''; // 缓存的key
    private $_cache_security_key = '';
    private $_blnUsedCache = false; // 取Cache输出的是吗?
    
    /**
     * 构造方法
     */
    public function __construct() {
        // TODO
    }
    
    /**
     * 默认的首页
     * --- 若无特别需求,请勿修改此函数,谢谢 ---
     */
    public function index() {
        echo 'Waiting for index page...';
    }
    
    /**
     * _remap() 方法
     */
    public function _remap($strAction) {
        Helper::jsLoadTo('/', '页面不存在!');
    }
    
    /**
     * 缓存设置,此处可以自定义
     */
    private function _setCache() {
        if ($this->_cache_seconds > 0) {
            $strPath = CACHES_PATH . '/' . $this->_cache_dir;
            !file_exists($strPath) && mkdir($strPath, 0777);
            $strPath = CACHES_PATH . '/' . $this->_cache_security_key;
            !file_exists($strPath) && mkdir($strPath, 0777);
            unset($strPath);
            phpFastCache::$storage = 'files';
            phpFastCache::$securityKey = $this->_cache_security_key;
            phpFastCache::$path = CACHES_PATH;
        }
    }
    
    /**
     * 缓存
     */
    protected function _cache($intSeconds = 0, $strExt = '') {
        if ($intSeconds > 0) {
            ob_start();
            $this->_cache_seconds = $intSeconds;
            $this->_cache_key = _ROUTE_M_ . '_' . _ROUTE_C_ . '_' . _ROUTE_A_;
            $this->_cache_security_key = $this->_cache_dir . '/' . md5($this->_cache_key);
            is_string($strExt) && !empty($strExt) && $this->_cache_key .= '_' . $strExt;
            $this->_setCache();
            $strContent = phpFastCache::get($this->_cache_key);
            if (null !== $strContent) {
                $this->_blnUsedCache = true;
                echo $strContent;
                exit();
            }
        }
    }
    
    /**
     * 析构函数
     */
    public function __destruct() {
        if ($this->_cache_seconds > 0 && !$this->_blnUsedCache) {
            if (!defined('DO_NOT_CACHE_ME')) {
                $this->_setCache();
                phpFastCache::set($this->_cache_key, ob_get_contents(), $this->_cache_seconds, false);
            }
            ob_flush();
        }
    }
    
    /**
     * 输出页面,并按需要做缓存
     */
    protected function _display($strPath, $arrAssign = null) {
        $strPath = TPL_PATH . '/' . $strPath;
        if (file_exists($strPath)) {
            if (!$this->_blnUsedCache) {
                is_array($arrAssign) && count($arrAssign) > 0 && extract($arrAssign);
                include($strPath);
            }
        } else {
            $this->_cache_seconds = 0;
            echo 'Template error : ' . $strPath;
        }
    }
}