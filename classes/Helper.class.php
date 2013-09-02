<?php
/**
 * 常用的函数
 */
!defined('IN_FRAME') && die('404 Page');
class Helper {
    
    /**
     * 跳转
     * --- 若无特别需求,请勿修改此函数,谢谢 ---
     * @param $strUrl 要跳转到的链接
     * @param $strMsg 跳转前的提示信息
     */
    public static function jsLoadTo($strUrl = '/', $strMsg = '') {
        if ('/' == $strUrl) {
            $strUrl = HTTP_URL;
        }
        $strAlert = !empty($strMsg) ? 'alert("' . addslashes($strMsg) . '");' : '';
        $strJs = <<<JS
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<script type="text/javascript">
{$strAlert}
window.top.location.href = "{$strUrl}";
</script>
</body>
</html>
JS;
        define('DO_NOT_CACHE_ME', 'yes');
        die($strJs);
    }
    
    /**
     * 加载模型
     * --- 若无特别需求,请勿修改此函数,谢谢 ---
     * @param $strName 模型名称
     * @param $blnInit 是否立即单态实例(是否需要,有待证实)
     */
    public static function loadModel($strName, $blnInit = true) {
        static $arrSingleModels = array();
        $objRe = null;
        if (!empty($strName)) {
            $strKey = md5($strName);
            if (isset($arrSingleModels[$strKey])) {
                $objRe = $arrSingleModels[$strKey];
            } else {
                $strClass = 'm_' . $strName;
                if (class_exists($strClass, true)) {
                    if ($blnInit) {
                        $objRe = new $strClass;
                        $arrSingleModels[$strKey] = $objRe;
                    } else {
                        $objRe = true;
                    }
                }
            }
        }
        return $objRe;
    }
    
    /**
     * 取随机字符串
     */
    public static function getRandStr($how = 4, $only = '') {
        srand((double)microtime() * 1000000); // 初始化随机数种子
        $alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; // 验证码内容1:字母
        $number = "0123456789"; // 验证码内容2:数字
        if ('number' == $only) {
            $alpha = $number;
        } elseif ('alpha' == $only) {
            $number = $alpha;
        }
        $randcode = '';
        $i = 0;
        while ($i < $how) {
            $i ++;
            $str = mt_rand(0, 1) ? $alpha : $number;
            $randcode .= substr($str, mt_rand(0, (strlen($str) - 1)), 1); // 逐位加入验证码字符串
        }
        return $randcode;
    }
    
    /**
     * 是Ajax请求吗?
     */
    public static function isAjaxRequest() {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    }
    
    /**
     * 处理单个上传的文件
     */
    public static function saveUploadedFile($strField, $arrAllowed = array('.jgp', '.gif', '.png'), $arrNotAllowed = array('.php', '.exe'), $intMaxSize = 10485760) {
        $arrRe = array('ok' => false, 'path' => '', 'msg' => '');
        if ($_FILES && $_FILES[$strField] && $_FILES[$strField]['name']) {
            if ($_FILES[$strField]['size'] > $intMaxSize) {
                $arrRe['msg'] = 'ERR_1'; // 文件过大
            } else {
                $strExt = substr($_FILES[$strField]['name'], strrpos($_FILES[$strField]['name'], '.'));
                $strExt = strtolower($strExt);
                if (is_array($arrAllowed) && !in_array($strExt, $arrAllowed)) {
                    $arrRe['msg'] = 'ERR_2'; // 不允许此类型文件
                } elseif (is_array($arrNotAllowed) && in_array($strExt, $arrNotAllowed)) {
                    $arrRe['msg'] = 'ERR_2'; // 不允许此类型文件
                } else {
                    $strPath = UPLOADS_PATH . '/' . date('y');
                    !file_exists($strPath) && mkdir($strPath, 0777);
                    $strPath .= '/' . date('m');
                    !file_exists($strPath) && mkdir($strPath, 0777);
                    $strPath .= '/' . date('d');
                    !file_exists($strPath) && mkdir($strPath, 0777);
                    $strFileName = date('His') . self::getRandStr() . $strExt;
                    if (move_uploaded_file($_FILES[$strField]['tmp_name'], $strPath . '/' . $strFileName)) {
                        $arrRe['ok'] = true;
                        $arrRe['path'] = str_replace(ROOT_PATH, '', $strPath) . '/' . $strFileName;
                    } else {
                        $arrRe['msg'] = 'ERR_3'; // 上传最终失败
                    }
                }
            }
        } else {
            $arrRe['msg'] = 'ERR_0'; // 没有文件
        }
        return $arrRe;
    }
}