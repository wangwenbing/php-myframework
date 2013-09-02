<?php
/* --- 若无特别需求,请勿修改此文件,谢谢 --- */
/**
 * 分页类
 */
!defined('IN_FRAME') && die('404 Page');
class Pager {
    public $blnNeedMore = true;
    public $arrCurrGet = array();
    public $strPageUrl = '';
    public $strVarName = 'page';
    public $intCurrPage = 1;
    public $intItemsCount = null;
    public $intPagesCount = null;
    public $blnReturnEmpty = false;
    public $intItemsPerPage = 10;
    public $strHtmlStart = '<span>';
    public $strHtmlEnd = '</span>';
    public $strCurrCssClass = '_pager_cur';
    public $strFirst = '第一页';
    public $strEnd = '末尾页';
    public $strPrevious = '上一页';
    public $strNext = '下一页';
    public $intNumShow = 5;
    public $strDivCssClass = '_pager';
    
    public function __construct($strPageUrl, $arrGet, $intItemsCount) {
        $this->strPageUrl = $strPageUrl;
        $this->arrCurrGet = $arrGet;
        $this->intItemsCount = $intItemsCount;
    }
    
    private function GenUrl($intPageNum) {
        $strRe = $this->strPageUrl . '?';
        $arrGet = $this->arrCurrGet;
        $arrGet[$this->strVarName] = $intPageNum;
        $blnFirst = true;
        foreach ($arrGet as $key => $val) {
            $blnFirst ? $blnFirst = false : $strRe .= '&';
            $strRe .= $key . '=' . urlencode($val);
        }
        return $strRe;
    }
    
    public function genHtml() {
        $arrGet = $this->arrCurrGet;
        isset($arrGet[$this->strVarName]) && $this->intCurrPage = $arrGet[$this->strVarName];
        $this->intPagesCount = ceil(($this->intItemsCount) / ($this->intItemsPerPage));
        if ($this->intPagesCount == 0) {
            $this->intPagesCount = 1;
        }
        if ($this->intPagesCount == 1 && $this->blnReturnEmpty) {
            return '';
        }
        $strReStart = '<div class="' . $this->strDivCssClass . '">';
        $strRe = '';
        $intForStart = ($this->intCurrPage) - ($this->intNumShow);
        $intForEnd = ($this->intCurrPage) + ($this->intNumShow);
        // 第一页
        if ($intForStart > 1) {
            $strRe .= $this->strHtmlStart . '<a href="' . $this->GenUrl(1) . '">' . $this->strFirst . '</a>' . $this->strHtmlEnd;
        }
        // 上一页
        if ($this->intCurrPage > 1) {
            $strRe .= $this->strHtmlStart . '<a href="' . $this->GenUrl(($this->intCurrPage) - 1) . '">' . $this->strPrevious . '</a>' . $this->strHtmlEnd;
        }
        // 中间部分
        if ($intForStart > 1) {
            $strRe .= '&nbsp;...';
        }
        for ($i = $intForStart; $i <= $intForEnd; $i ++) {
            if ($i >= 1 && $i <= $this->intPagesCount) {
                if ($i == $this->intCurrPage) {
                    if ($this->intPagesCount > 1) {
                        $strRe .= str_replace('>', '', $this->strHtmlStart) . ' class="' . $this->strCurrCssClass . '">' . $i . $this->strHtmlEnd;
                    }
                } else {
                    $strRe .= $this->strHtmlStart . '<a href="' . $this->GenUrl($i) . '">' . $i . '</a>' . $this->strHtmlEnd;
                }
            }
        }
        if ($intForEnd < $this->intPagesCount) {
            $strRe .= '...';
        }
        // 下一页
        if ($this->intCurrPage < $this->intPagesCount) {
            $strRe .= $this->strHtmlStart . '<a href="' . $this->GenUrl(($this->intCurrPage) + 1) . '">' . $this->strNext . '</a>' . $this->strHtmlEnd;
        }
        // 末尾页
        if ($intForEnd < $this->intPagesCount) {
            $strRe .= $this->strHtmlStart . '<a href="' . $this->GenUrl($this->intPagesCount) . '">' . $this->strEnd . '</a>' . $this->strHtmlEnd;
        }
        // 共xx页
        if ($this->blnNeedMore) {
            !empty($strRe) && $strRe .= '&nbsp;&nbsp;';
            $strRe .= '第' . ($this->intCurrPage) . '页，共' . ($this->intPagesCount) . '页，每页' . $this->intItemsPerPage . '条，共' . ($this->intItemsCount) . '条结果';
        }
        $strRe .= '</div>';
        return $strReStart . $strRe;
    }
}