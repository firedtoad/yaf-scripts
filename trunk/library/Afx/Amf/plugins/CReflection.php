<?php

class Afx_Amf_plugins_CReflection {
    var $_fileinfo;
    
    function __construct($file){
        if (!is_file($file))
        {
            return false;
        }
        $this->_fileinfo = file_get_contents($file);
    }
    
    
    
    function getDocComment()
    {
        if (preg_match('%/\*(?:(?!\*/).)+\*/(?=\s+class)%s', $this->_fileinfo, $regs)) {
            $result = $regs[0];
        } else {
            $result = "";
        }
        return $result;
    }
    
    
    function getMethods()
    {
        preg_match_all('%(?:\}|;|/)\s+(?:p(?:ublic|rivate|rotected)\s+)?function\s+([^()]+)\s*\([^)]*\)%s', $this->_fileinfo, $result, PREG_PATTERN_ORDER);
        $rs = $result[1];
        $arr = array();
        foreach ($rs as $value)
        {
            $strRegex = '%(?P<comment>/\*(?:(?!\*/).)+\*/)?\s+(?P<access>p(?:ublic|rivate|rotected)\s+)?function\s+'.trim($value).'\s*\((?P<param>[^)]*)\)%s';
            $strComment = $strFunction = $strParamenters = '';
            if (preg_match($strRegex, $this->_fileinfo, $regs)) {
                $strComment = isset($regs['comment']) ? $regs['comment'] : '';
                $strFunction = $value;
                $strParamenters =isset($regs['param']) ? $regs['param'] : '';
                $strAccess = isset($regs['access']) ? $regs['access'] : 'public';
            }
                $arr[] = new CReflectionMethod($strFunction, $strComment, $strParamenters,$strAccess);
        }
        return $arr;
    }
}

class CReflectionMethod {
    var $_name;
    var $_comment;
    var $_paramenter;
    var $_access;
    
    function __construct($strFunction, $strComment, $strParamenters, $strAccess){
        $this->_name = $strFunction;
        $this->_comment = $strComment;
        $this->_paramenter = $strParamenters;
        $this->_access = $strAccess;
    }
    
    
    
    function getDocComment()
    {
        return $this->_comment;
    }
    
    
    function getParameters()
    {
        $arr = array();
        $arrT = explode(',', $this->_paramenter);
        if (trim($this->_paramenter) == '')
        {
            return $arr;
        }
        foreach ($arrT as $value)
        {
            $arr[] = trim($value);
        }
        return $arr;
    }
}
?>