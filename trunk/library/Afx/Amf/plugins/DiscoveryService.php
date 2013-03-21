<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Discovery
 */

/**
 * analyses existing services. Warning: if 2 or more services have the same name, t-only one will appear in the returned data, 
 * as it is an associative array using the service name as key. 
 * @package Amfphp_Plugins_Discovery
 * @author Ariel Sommeria-Klein
 */
class Afx_Amf_plugins_DiscoveryService {

    /**
     * @see AmfphpDiscovery
     * @var array of strings(patterns)
     */
    public $excludePaths;

    /**
     * paths to folders containing services(relative or absolute). set by plugin.
     * @var array of paths
     */
    public $serviceFolderPaths;

    /**
     *
     * @var array of ClassFindInfo. set by plugin.
     */
    public $serviceNames2ClassFindInfo;

    /**
     * restrict access to amfphp_admin. 
     * @var boolean
     */
    public static $restrictAccess;

    /**
     * get method roles
     * @param string $methodName
     * @return array
     */
    public function _getMethodRoles($methodName) {
        if (self::$restrictAccess) {
            return array('amfphp_admin');
        }
    }

    /**
     * finds classes in folder. If in subfolders add the relative path to the name.
     * recursive, so use with care.
     * @param string $rootPath
     * @param string $subFolder
     * @return array
     */
    protected function searchFolderForServices($rootPath, $subFolder) {
        $ret = array();
        $folderContent = scandir($rootPath . $subFolder);

        if ($folderContent) {
            foreach ($folderContent as $fileName) {
                //add all .php file names, but removing the .php suffix
                if (strpos($fileName, ".php")) {
                    $fullServiceName = $subFolder . substr($fileName, 0, strlen($fileName) - 4);
                    $ret[] = $fullServiceName;
                } else if ((substr($fileName, 0, 1) != '.') && is_dir($rootPath . $subFolder . $fileName)) {
                    $ret = array_merge($ret, $this->searchFolderForServices($rootPath, $subFolder . $fileName . '/'));
                }
            }
        }
        return $ret;
    }

    /**
     * returns a list of available services
     * @param array $serviceFolderPaths
     * @param array $serviceNames2ClassFindInfo
     * @return array of service names
     */
    protected function getServiceNames(array $serviceFolderPaths) {
        $ret = array();
        foreach ($serviceFolderPaths as $serviceFolderPath) {
            $ret = array_merge($ret, $this->searchFolderForServices($serviceFolderPath, ''));
        }

/*         foreach ($serviceNames2ClassFindInfo as $key => $value) {
            if (strpos($key,'mfphp') === false)
            {
                $ret[] = $key;
            }
        } */
        return $ret;
    }

    /**
     * extracts 
     * - types from param tags. type is first word after tag name, name of the variable is second word ($ is removed)
     * - return tag
     * 
     * @param string $comment 
     * @return array{'returns' => type, 'params' => array{var name => type}}
     */
    protected function parseMethodComment($comment) {
        //get rid of phpdoc formatting
        $comment = str_replace('/**', '', $comment);
        $comment = str_replace('*', '', $comment);
        $comment = str_replace('*/', '', $comment);
        $exploded = explode('@', $comment);
        $ret = array();
        $params = array();
        foreach ($exploded as $value) {
            if (strtolower(substr($value, 0, 5)) == 'param') {
                $words = explode(' ', $value);
                $type = trim($words[1]);
                $varName = trim(str_replace('$', '', $words[2]));
                $params[$varName] = $type;
            } else if (strtolower(substr($value, 0, 6)) == 'return') {

                $words = explode(' ', $value);
                $type = trim($words[1]);
                $ret['return'] = $type;
            }
        }
        $ret['param'] = $params;
        if (!isset($ret['return'])) {
            $ret['return'] = '';
        }
        return $ret;
    }

    /**
     * does the actual collection of data about available services
     * @return array of AmfphpDiscovery_ServiceInfo
     */
    public function getServices() {
        $serviceNames = $this->getServiceNames($this->serviceFolderPaths);
        $ret = $ret1 = array();
//        require_once AMFPHP_ROOTPATH.'Plugins/AmfphpDiscovery/CReflection.php';
        foreach ($serviceNames as $serviceName) {
/*             $methods = array();
            $objC = new CReflection(APP_PATH.'/app/controllers/'.$serviceName.'.php');
            $objComment = $objC->getDocComment();
            $arrMethod = $objC->getMethods();
            foreach ($arrMethod as $objMethods)
            {
                $methodComment = $objMethods->getDocComment();
                $parsedMethodComment = $this->parseMethodComment($methodComment);
                $arrParamenter = $objMethods->getParameters();
                foreach ($arrParamenter as $Paramenter)
                {
                    $parameterInfo = new AmfphpDiscovery_ParameterDescriptor($Paramenter, '');
                    $parameters[] = $parameterInfo;
                }
                $methods[$objMethods->_name] = new AmfphpDiscovery_MethodDescriptor($objMethods->_name, $parameters, $methodComment, $parsedMethodComment['return']);
            }

            $ret[$serviceName] = new AmfphpDiscovery_ServiceDescriptor($serviceName, $methods, $objComment); */
            $ret1[] = array('label'=>$serviceName,'date'=>'');
        }
//        var_dump($ret);exit();
        //note : filtering must be done at the end, as for example excluding a Vo class needed by another creates issues
        foreach ($ret as $serviceName => $serviceObj) {
            foreach (self::$excludePaths as $excludePath) {
                if (strpos($serviceName, $excludePath) !== false) {
                    unset($ret[$serviceName]);
                    break;
                }
            }
        }
        return $ret1;
    }
    
    public function describeService($data) {
        $serviceName = $data['label'];
        $ret = $methods = array();
        if (!is_file(APP_PATH.'/app/controllers/'.$serviceName.'.php'))
        {
            return $ret;
        }
        $objC = new Afx_Amf_plugins_CReflection(APP_PATH.'/app/controllers/'.$serviceName.'.php');
        $objComment = $objC->getDocComment();
        $arrMethod = $objC->getMethods();
        foreach ($arrMethod as $objMethods)
        {
            $methodComment = $objMethods->getDocComment();
            $parsedMethodComment = $this->parseMethodComment($methodComment);
            $arrParamenter = $objMethods->getParameters();
/*             foreach ($arrParamenter as $Paramenter)
            {
                //$parameterInfo = new Afx_Amf_plugins_ParameterDescriptor($Paramenter, '');
                //$parameters[] = $parameterInfo;
                $parameters[] = $Paramenter;
            } */
            $methods[$objMethods->_name] = new Afx_Amf_plugins_MethodDescriptor($methodComment, $arrParamenter, $objMethods->_access);
        }
        $ret[] = $methods;
        $ret[] = $objComment;
        //$ret[] = new Afx_Amf_plugins_ServiceDescriptor($serviceName, $methods, $objComment);
        return $ret;
    }

}

?>
