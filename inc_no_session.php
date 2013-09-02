<?php
/**
 * 页面的开始时间
 */
define('_PAGE_START_TIME_', microtime(true));

/**
 * 字符编码
 */
header("Content-type: text/html; charset=utf-8");

/**
 * 包含配置文件
 */
include(dirname(__FILE__) . '/conf.php');

/**
 * 类的自动加载
 */
function autoLoad($strClassName) {
    $strClassPath = CLASSES_PATH;
    $strPre = substr($strClassName, 0, 2);
    if ('c_' == $strPre) {
        $strClassPath = CTRLS_PATH;
    } elseif ('m_' == $strPre) {
        $strClassPath = MODELS_PATH;
    }
    $strClassPath .= '/' . $strClassName . '.class.php';
    if (file_exists($strClassPath)) {
        require($strClassPath);
    }
}
spl_autoload_register('autoLoad');

/**
 * 输出短函数
 */
function p($str = null, $strDefault = null) {
    if (!(null === $str || '' === $str)) {
        print($str);
    } elseif (!(null === $strDefault || '' === $strDefault)) {
        print($strDefault);
    }
}