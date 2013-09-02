<?php
/* --- 若无特别需求,请勿修改此文件,谢谢 --- */
define('IN_FRAME', 'YES');
include(dirname(__FILE__) . '/inc.php');
$_m_ = '';
$_c_ = 'index';
$_a_ = 'index';
if (USE_PATH_INFO && is_array($_SERVER) && isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $_strPathInfo_ = $_SERVER['PATH_INFO'];
    (defined('URL_EXT') && '' != URL_EXT) && ($_strPathInfo_ = str_replace(URL_EXT, '', $_strPathInfo_));
    $_arrPathInfo_ = explode('/', substr($_strPathInfo_, 1));
    $_cntPathInfo_ = count($_arrPathInfo_);
    if ($_cntPathInfo_ == 2) {
        !empty($_arrPathInfo_[0]) && $_c_ = $_arrPathInfo_[0];
        !empty($_arrPathInfo_[1]) && $_a_ = $_arrPathInfo_[1];
    } elseif ($_cntPathInfo_ == 3) {
        !empty($_arrPathInfo_[0]) && $_m_ = $_arrPathInfo_[0];
        !empty($_arrPathInfo_[1]) && $_c_ = $_arrPathInfo_[1];
        !empty($_arrPathInfo_[2]) && $_a_ = $_arrPathInfo_[2];
    }
    unset($_strPathInfo_);
    unset($_cntPathInfo_);
    unset($_arrPathInfo_);
} elseif (is_array($_GET) && isset($_GET['c']) && !empty($_GET['c'])) {
    $_c_ = $_GET['c'];
    isset($_GET['a']) && !empty($_GET['a']) && $_a_ = $_GET['a'];
    isset($_GET['m']) && !empty($_GET['m']) && $_m_ = $_GET['m'];
}
$_blnOk_ = false;
$_strCtrlName_ = 'c_' . $_c_;
$_blnM_ = !empty($_m_);
if ($_blnM_) {
    $_strPath_ = CTRLS_PATH . '/' . $_m_ . '/' . $_strCtrlName_ . '.class.php';
    file_exists($_strPath_) && require($_strPath_);
    unset($_strPath_);
}
if (class_exists($_strCtrlName_, !$_blnM_)) {
    $_objCtrl_ = new $_strCtrlName_;
    unset($_strCtrlName_);
    unset($_blnM_);
    $_p_ = '';
    !is_callable(array($_objCtrl_, $_a_)) && (list($_p_, $_a_) = array($_a_, '_remap'));
    define('_ROUTE_M_', $_m_);
    define('_ROUTE_C_', $_c_);
    define('_ROUTE_A_', $_a_);
    call_user_func(array($_objCtrl_, $_a_), $_p_);
    $_blnOk_ = true;
}
if (!$_blnOk_) {
    Helper::jsLoadTo('/', '页面不存在!');
}