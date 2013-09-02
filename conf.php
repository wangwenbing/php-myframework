<?php
/**
 * 报错等级
 */
!defined('IN_FRAME') && die('404 Page'); // 非法请求,可以自定义
error_reporting(E_ALL & ~E_NOTICE); // 严格要求自己

/**
 * 数据库配置
 */
define('DB_HOST', 'localhost'); // 数据库主机
define('DB_USER', 'root'); // 用户名
define('DB_PASD', 'root'); // 密码
define('DB_BASE', 'mysql'); // 库名
define('DB_PORT', 3306); // 端口
define('DB_CHARSET', 'utf8'); // 编码
define('DB_COLLATE', 'utf8_general_ci'); // 字符集

/* --- 若无特别需求,请勿修改下面的配置,谢谢 --- */

/**
 * 路径相关的设置
 */
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__))); // 站点根目录
define('CLASSES_PATH', ROOT_PATH . '/classes'); // 公共类
define('CACHES_PATH', ROOT_PATH . '/caches'); // 缓存目录
define('UPLOADS_PATH', ROOT_PATH . '/uploads'); // 上传文件的目录
define('CTRLS_PATH', ROOT_PATH . '/controllers'); // 控制器类目录,所有的类名以"c_"打头
define('MODELS_PATH', ROOT_PATH . '/models'); // 模型类目录,所有的类名以"m_"打头
define('TPL_PATH', ROOT_PATH . '/templates'); // 模板文件目录

/**
 * 链接的设置
 */
define('HTTP_URL', 'http://' . $_SERVER['HTTP_HOST']); // 首页链接
define('JS_URL', HTTP_URL . '/statics/js'); // JS文件根链接
define('INC_JQUERY', '<script type="text/javascript" src="' . JS_URL . '/jquery-1.9.0.min.js"></script>'); // 方便模板引入JQuery
define('CSS_URL', HTTP_URL . '/statics/css'); // CSS文件根链接
define('IMGS_URL', HTTP_URL . '/statics/images'); // 图片文件根链接

/**
 * 路由
 */
define('USE_PATH_INFO', true); // 使用PATH_INFO来定义路由吗?
define('URL_EXT', '.html'); // 当USE_PATH_INFO == true的时候有效

/**
 * 设置时区
 */
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Shanghai'); // 默认是北京时间
}