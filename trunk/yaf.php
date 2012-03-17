#!/usr/bin/env php
<?php
date_default_timezone_set('Asia/Shanghai');
$table_prefix = '';
function usage ()
{
    echo <<<H
usage php yaf.php [options]
Options
path=[dir] the directory where you place the project
-c=controllername  create controller with given controllername
-m=modelname       create model with  given modelname
-a=actioname       create action with given actioname
c=controllername   the same as -c
m=modelname        the same as -m
a=actioname        the same as -a
Examples
php yaf.php path=.     create project in current folder
php yaf.php -c=Add     create Controller Add
php yaf.php -m=Message create Model Message
php yaf.php -c=Add -a=Add add an action named Add to Add controller
Notice -c and -a options must be use at the same time
H;
}
$options = array();
$template = '<?php
class #CLASSController extends Yaf_Controller_abstract {
   public function indexAction() {
       echo "Hi from #CLASS action";
   }
}
';
$view_temp = '<html>
<head>
<title></title>
</head>
<body>
 Hi From #CLASS template!
</body>
</html>';
$m_temp = "
<?php
class #CLASS extends Afx_Module_Abstract
{
    protected \$_tablename = '$table_prefix#TABLE';
    /**
    * @var #CLASS \$_instance
    */
    protected static \$_instance = NULL;

    /**
     * @return  #CLASS
     */
    public function __construct ()
    {
    }
    /**
     * @return #CLASS
     */
    public static function Instance ()
    {
        if (NULL === self::\$_instance) {
            self::\$_instance = new self();
        }
        return self::\$_instance;
    }
}
";
function tr ($str)
{
    $len = strlen($str);
    $str = strtolower($str);
    $rs .= strtoupper($str[0]);
    for ($i = 1; $i < $len; ++ $i) {
        $rs .= $str[$i];
        if ($str[$i] == '_') {
            $str[$i + 1] = strtoupper($str[$i + 1]);
        }
    }
    return $rs;
}
function parse_command_line ($str)
{
    global $options;
    $arr = explode('=', $str);
    if (isset($arr[0]) && isset($arr[1])) {
        $options[$arr[0]] = $arr[1];
    }
}
if (! isset($argv) || ! is_array($argv))
    exit("no argv specific\n");
array_map('parse_command_line', $argv);
$cwd = getcwd();
$lock_file = $cwd . "/.yaf_lock";
if (file_exists($lock_file)) {
    if (isset($options['c']) || isset($options['-c'])) {
        if (empty($options['c']) && empty($options['-c'])) {
            exit("controller is missing");
        }
        $class = (isset($options['c']) ? $options['c'] : $options['-c']);
        $class = tr($class);
        $class_file = $cwd . "/application/controllers/" . strtolower(tr($class)) . ".php";
        $view_path = $cwd . "/application/views/" . strtolower($class);
        if (file_exists(($class_file))) {
            if (! empty($options['a']) || ! empty($options['-a'])) {
                $action = (isset($options['a']) ? $options['a'] : $options['-a']);
                $class_temp = file_get_contents($class_file);
                if (preg_match("/$action" . "Action\\(\\)/", $class_temp)) {
                    exit("Action Exists\n");
                }
                $lastindex = strripos($class_temp, '}');
                $class_temp = substr($class_temp, 0, $lastindex);
                $action_temp = "   public function $action" . "Action(){\n   }\n";
                $class_temp .= $action_temp . "}";
                echo "write Action \n $class_file \n";
                file_put_contents($class_file, $class_temp);
                $view_file = $view_path . "/" . strtolower($action) . ".phtml";
                echo "write view \n $view_file \n";
                $view_temp = str_replace('#CLASS', $action, $view_temp);
                file_put_contents($view_file, $view_temp);
                exit();
            }
            exit('controller exists!' . "\n");
        }
        $class_temp = str_replace('#CLASS', $class, $template);
        $view_temp = str_replace('#CLASS', $class, $view_temp);
        echo "class file=$class_file\nclass template=$class_temp";
        echo "write class file\n";
        file_put_contents($class_file, $class_temp);
        if (! file_exists($view_path)) {
            echo "mkdir $view_path\n";
            mkdir($view_path, 777, TRUE);
        }
        $view_file = $view_path . "/index.phtml";
        echo "write view file\n";
        file_put_contents($view_file, $view_temp);
        exit("done\n");
    }
    if (isset($options['m']) || isset($options['-m'])) {
        if (empty($options['m']) && empty($options['-m'])) {
            exit("module is missing");
        }
        $class = (isset($options['m']) ? $options['m'] : $options['-m']);
        $class_temp = str_replace('#TABLE', ($class), $m_temp);
        $class = str_replace('_', '', tr($class));
        $class_file = $cwd . "/application/models/" . ($class) . ".php";
        if (file_exists($class_file)) {
            exit("File exists\n");
        }
        $class_temp = str_replace('#CLASS', $class, $class_temp);
        echo "class file=$class_file\nclass template=$class_temp";
        echo "write class file\n";
        file_put_contents($class_file, $class_temp);
        exit("done\n");
    }
    exit(usage());
}
if (! isset($options['path']))
    exit(usage());
$path = (isset($options['path']) ? $options['path'] : dirname(__FILE__));
if (! file_exists($path)) {
    mkdir($path, 0777, TRUE);
}
$path = realpath($path);
$lock_file = $path . "/.yaf_lock";
$conf = array('path' => './',
'paths' => array($path . '/conf', $path . '/application',
$path . '/application/controllers', $path . '/application/views',
$path . '/application/views/index', $path . '/application/views/error',
$path . '/application/modules', $path . '/application/library',
$path . '/application/library/', $path . '/application/models',
$path . '/application/plugins', $path . '/Public'),
'files' => array(
array('name' => $path . '/index.php',
'content' => "<?php//yaf 入口文件
date_default_timezone_set('Asia/Shanghai');
error_reporting(-1);
//ini_set('apc.debug', '0');
if (PHP_SAPI == 'cli') {
    define('APP_PATH', dirname(realpath(__FILE__)));
} else {
    define('APP_PATH', \$_SERVER['DOCUMENT_ROOT']);
}
/**
 * yaf默认只支持application/library 目录下类的加载
 * 并且不允许Yaf打头的第三方类出现
 * 这里注册一个__autoload顺序加载application 下所有文件夹下的类
 * Yaf 默认认为每个类都应该有下划线每个下划线代表一层目录
 */
if (file_exists(APP_PATH . '/conf/auto.php')) {
    require_once APP_PATH . '/conf/auto.php';
    spl_autoload_register('__autoload');
}
\$app = new Yaf_Application(APP_PATH . '/conf/application.ini', 'production');
\$app->bootstrap()->run();"),
array('name' => $path . '/.htaccess',
'content' => 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^/Public/(.*\.(js|ico|gif|jpg|png|css|bmp|html|wsdl|pdf|xls)$) /Public/$1 [L]
RewriteRule ^/.* /index.php [L]'),
array('name' => $path . '/conf/application.ini',
'content' => '[production]
application.directory=APP_PATH "/application"
application.dispatcher.catchException=TRUE
application.use_spl_autoload=1
smarty.template_dir=APP_PATH "/application/views/template"
smarty.cache_dir=APP_PATH "/application/views/cache"
smarty.compile_dir=APP_PATH "/application/views/cache"
smarty.cache=FALSE
smarty.debug=FALSE
'),
array('name' => $path . '/conf/auto.php',
'content' => '<?php
$root = $_SERVER["DOCUMENT_ROOT"] . "/application";
$load_paths = "$root/models;$root/modules;$root/plugins;$root/library";
function __autoload ($class_name)
{
//    ob_clean();
    global $load_paths,$root;
    $paths = explode(";", $load_paths);
    if (strstr($class_name, "_")) {
        $class_name = str_ireplace("_", "/", $class_name);
    }
    if (is_array($paths)) {
        foreach ($paths as $path) {
             if($path=="$root/models"){
                $class_name = str_ireplace("/", "_", $class_name);
             }
            if (file_exists($path . "/" . $class_name . ".php")) {
                require_once $path . "/" . $class_name . ".php";
             }
        }
    }
    if (file_exists($class_name . "php")) {
        require_once $class_name . "php";
    }
}'),
array('name' => $path . '/conf/conf.php',
'content' => "<?php
return array(
 'db'=>array(
  'type'=>'mysql',
  'master'=>array('host'=>'192.168.117.129','port'=>'3306','user'=>'firedtoad','password'=>'zwhdhrdj','dbname'=>'bgpointdb','charset'=>'utf8'),
  'slave'=>array(
                array('host'=>'192.168.117.129','port'=>'3306','user'=>'firedtoad','password'=>'zwhdhrdj','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.117.129','port'=>'3306','user'=>'firedtoad','password'=>'zwhdhrdj','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.117.129','port'=>'3306','user'=>'firedtoad','password'=>'zwhdhrdj','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.117.129','port'=>'3306','user'=>'firedtoad','password'=>'zwhdhrdj','dbname'=>'bgpointdb','charset'=>'utf8')
               ),
        ),
/* 'db'=>array(
  'type'=>'mysql',
  'master'=>array('host'=>'10.100.200.22','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
  'slave'=>array(
                array('host'=>'10.100.200.22','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'10.100.200.22','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'10.100.200.22','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'10.100.200.22','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8')
               ),
        ),*/
/* 'db'=>array(
  'type'=>'mysql',
  'master'=>array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
  'slave'=>array(
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8')
               ),
        ),*/
/* 'db'=>array(
  'type'=>'mysql',
  'master'=>array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
  'slave'=>array(
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8'),
                array('host'=>'192.168.188.130','port'=>'3306','user'=>'openmall','password'=>'123','dbname'=>'bgpointdb','charset'=>'utf8')
               ),
            ),*/
  'memcache'=>array(
    'type'=>'memcache',
    'master'=>array('host'=>'192.168.117.129','port'=>'11211'),
    'slave'=>array(
            array('host'=>'192.168.117.129','port'=>'11211'),
            array('host'=>'192.168.117.129','port'=>'11211'),
            array('host'=>'192.168.117.129','port'=>'11211'),
            )
  ),
/*  'memcache'=>array(
    'type'=>'memcache',
    'master'=>array('host'=>'192.168.149.43','port'=>'11211'),
    'slave'=>array(
            array('host'=>'192.168.149.43','port'=>'11211'),
            array('host'=>'192.168.149.43','port'=>'11211'),
            array('host'=>'192.168.149.43','port'=>'11211'),
            )
  ),*/
  'mongo'=>array(
   'type'=>'mongo',
   'master'=>array('host'=>'127.0.0.1','port'=>'27017','db'=>'pstore','collection'=>'pstore'),
   'slave'=>array(
                array('host'=>'127.0.0.1','port'=>'27017','db'=>'pstore','collection'=>'pstore'),
                array('host'=>'127.0.0.1','port'=>'27017','db'=>'pstore','collection'=>'pstore'),
                array('host'=>'127.0.0.1','port'=>'27017','db'=>'pstore','collection'=>'pstore'),
                )
           ),
   'redis'=>array(
   'type'=>'redis',
   'master'=>array('host'=>'127.0.0.1','port'=>'27017'),
   'slave'=>array(
          array('host'=>'127.0.0.1','port'=>'6379'),
          array('host'=>'127.0.0.1','port'=>'6379'),
          array('host'=>'127.0.0.1','port'=>'6379'),
                )
           ),
   'service'=>'https://www.test.com/CASServer/custom/api/',
   'casServer'=>'www.test.com',
   'casUrl'=>'CASServer',
   'casLoginUrl'=>'https://www.test.com/CASServer/login?service=http://inner.pstore.com/Index/Index/Login',
   'casLogoutUrl'=>'https://www.test.com/CASServer/logout?service=http://inner.pstore.com/Index/Index/Logout',
/*   'casLoginUrl'=>'https://www.test.com/CASServer/login?service=http://pstore/Index/Index/Login',
   'casLogoutUrl'=>'https://www.test.com/CASServer/logout?service=http://pstore/Index/Index/',*/
   'api'=>array(
             'getUserInfo'=>array('http://10.100.200.12/CASManager/custom/api/getUserInfo.do','post'),
             'getPointsByUid'=>array('http://10.100.200.12/CASManager/custom/api/getPointsByUid.do','post'),
//             'customerUsePoints'=>array('http://192.168.127.79:8080/CASManager/custom/api/customerUsePoints.do','post'),
             't'=>array('http://pstore/Index/Store/t','post'),
             'customerUsePoints'=>array('http://10.100.200.12/CASManager/custom/api/customerUsePoints.do','post'),
//             'customerUsePoints'=>'http://192.168.188.114/CASManager/custom/api/customerUsePoints.do',
           ),
);"),
array('name' => $path . '/application/Bootstrap.php',
'content' => "<?php
/**
 *
 * 框架初始化类
 * @author firedtoad
 *
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
     /**
     * 初始化会话
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initSession (Yaf_Dispatcher \$dispatcher)
    {
        ini_set('session.save_handler', 'memcache');
        ini_set('session.save_path', \"tcp://192.168.117.129:11211\");
        session_start();
        header('content-type:text/html;charset=utf-8');
    }
    public function _initMemory(Yaf_Dispatcher \$dispatcher){
       //   ini_set('memory_limit', '2M');
//        echo  ini_get('memory_limit');
    }
    /**
     * 初始化文件日志
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initLog (Yaf_Dispatcher \$dispatcher)
    {
        Afx_Logger::\$_logpath = APP_PATH . '/log';
    }
    /**
     * 初始化数据库链接 mysql mongo memcache
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initDb (Yaf_Dispatcher \$dispatcher)
    {
        if (file_exists(APP_PATH . '/conf/conf.php')) {
            \$conf = include_once APP_PATH . '/conf/conf.php';
            try {
                Yaf_Registry::set('conf', \$conf);
                Afx_Db_Adapter::initOption(\$conf);
                Afx_Db_Mongo::setOptions(\$conf);
                Yaf_Registry::set('conf', \$conf);
                Afx_Module_Abstract::setAdapter(
                Afx_Db_Adapter::Instance());
                Afx_Db_Memcache::initOption(\$conf);
                Afx_Db_Adapter::\$debug = FALSE;
            } catch (Exception \$e) {
                Afx_Debug_Helper::print_r(\$e->getMessage());
                exit();
            }
        }
        if (file_exists(APP_PATH . '/conf/mapping.php')) {
            \$mapping = include APP_PATH . '/conf/mapping.php';
            Afx_Db_Adapter::setMapping(\$mapping);
        }
    }
    /**
     * 初始化模型层
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initModel (Yaf_Dispatcher \$dispatcher)
    {
        ob_start();
    }
    public function _initConfig (Yaf_Dispatcher \$dispatcher)
    {
        //		\$dispatcher->getRequest();
    }
    /**
     * 初始化命令行方式 供守护进程使用
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initCli (Yaf_Dispatcher \$dispatcher)
    {
        \$req = \$dispatcher->getRequest();
        if (\$req->isCli()) {
            global \$argc, \$argv;
            if (\$argc >= 2) {
                \$urls = explode('/', \$argv[1]);
                \$req->setControllerName(\$urls[0]);
                \$req->setActionName(\$urls[1]);
                \$dispatcher->disableView();
                 //                \$dispatcher->dispatch(\$req);
            }
        }
    }
     /**
      * 初始化smarty 插件
      * @param Yaf_Dispatcher \$dispatcher
      */
    public function _initSmarty (Yaf_Dispatcher \$dispatcher)
    {
        //\$conf = Yaf_Application::app()->getConfig()->get('smarty');
        // Afx_Debug_Helper::print_r(\$conf);
        //        Yaf_Registry::set('config', Yaf_Application::app()->getConfig());
        //        \$con = Yaf_Registry::get('smarty');
        //        \$smart_adapter = new Afx_Smarty_Adapter(NULL,
        //        array('cache' => \$conf->get('compile_dir'),
        //        'compile_dir' => \$conf->get('compile_dir'),
        //        'template_dir' => \$conf->get('template_dir'),
        //        'cache_dir' => \$conf->get('cache_dir'),
        //        'debug' => \$conf->get('debug'),
        //        ));
        \$dispatcher->disableView();
         //       \$dispatcher->setView(\$smart_adapter);
    }
    public function _initConf (Yaf_Dispatcher \$dispatcher)
    {}
    /**
     * 初始化CAS客户端
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initCas (Yaf_Dispatcher \$dispatcher)
    {

        if (file_exists(APP_PATH . '/conf/conf.php')) {
//                Afx_Debug_Helper::print_r(\$conf);
                  \$conf=Yaf_Registry::get('conf');
                try {
                    Afx_Cas_Helper::\$CASSERVER=\$conf['casServer'];
                    Afx_Cas_Helper::\$CASURL=\$conf['casUrl'];
                    Afx_Cas_Helper::\$CASLOGINURL=\$conf['casLoginUrl'];
                    Afx_Cas_Helper::\$CASLOGOUTURL=\$conf['casLogoutUrl'];
                } catch (Exception \$e) {
                      throw new Afx_Db_Exception('Init Cas failed', 1000);
                }
            }
    }

    /**
     * 初始化验证插件
     * @param Yaf_Dispatcher \$dispatcher
     */
    public function _initPlugin (Yaf_Dispatcher \$dispatcher)
    {
        if (file_exists(APP_PATH . '/conf/forms.php') &&
         file_exists(APP_PATH . '/conf/messages.php')) {
            \$forms = require_once APP_PATH . '/conf/forms.php';
            \$messages = require_once APP_PATH . '/conf/messages.php';
            Validate::setForms(\$forms);
            Validate::setMessages(\$messages);
        }
        \$form = Form::Instance();
        \$validate = Validate::Instance();
        \$ret = \$dispatcher->registerPlugin(\$form)->registerPlugin(\$validate);
    }
}
"),
array('name' => $path . '/application/controllers/Index.php',
'content' => '<?php
class IndexController extends Yaf_Controller_abstract {
   public function indexAction() {
     echo "Hi from yaf";
   }
}
 '),
array('name' => $path . '/application/controllers/Error.php',
'content' => '<?php
class ErrorController extends Yaf_Controller_Abstract
{
    public function errorAction(){
    $exception = $this->getRequest()->getException();
   try {
    throw $exception;
  } catch (Yaf_Exception_LoadFailed $e) {
       echo $e->getMessage();
  } catch (Yaf_Exception $e) {
       echo $e->getMessage();
  }
    }
}
 '),
array('name' => $path . '/application/views/index/index.phtml',
'content' => '<html>
 <head>
   <title>Hello World</title>
 </head>
 <body>
    Hellow World!
 </body>
</html>'),
array('name' => $path . '/application/views/error/error.phtml',
'content' => '
  error occured
 ')));
if (! file_exists($path)) {
    mkdir($path, 0777, TRUE);
}
if (is_array($conf['paths'])) {
    foreach ($conf['paths'] as &$k) {
        if (! file_exists($k)) {
            echo "make dir  $k" . "\n";
            mkdir($k);
        }
    }
}
if (is_array($conf['files'])) {
    if (! file_exists($lock_file)) {
        file_put_contents($lock_file, 'yaf_lock_file');
    }
    foreach ($conf['files'] as &$k) {
        if (isset($k['name'])) {
            echo 'create file  ', $k['name'] . "\n";
            $data = isset($k['content']) ? $k['content'] : '';
             print_r($k);
            file_put_contents($k['name'], $data);
        }
    }
}
if (file_exists('library')) {
    echo 'copy Lib', "\n";
    if (PHP_OS == 'Linux') {
        echo 'Linux copy Lib', "\n";
        `mv -f library $path/application/`;
    } else {
        echo 'Windows copy Lib', "\n";
        echo `move /Y library $path/application/`;
    }
}