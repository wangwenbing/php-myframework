<?php
/**
 * DES加解密
 */
!defined('IN_FRAME') && die('404 Page');
class DES {
    
    private $strKey = 'asJHjhiH';
    
    public function encode($strInput) {
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->strKey, $iv);
        $data = mcrypt_generic($td, $this->pkcs5_pad($strInput));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return base64_encode($data);
    }
    
    public function decode($strInput) {
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->strKey, $iv);
        $data = mdecrypt_generic($td, base64_decode($strInput));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $this->pkcs5_unpad($data);
    }
    
    function pkcs5_pad($text) {   
        $pad = 8 - (strlen($text) % 8);
        return $text . str_repeat(chr($pad), $pad);
    }
    
    function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}