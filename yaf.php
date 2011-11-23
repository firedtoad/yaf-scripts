#!/usr/bin/env php
<?php
date_default_timezone_set('Asia/Shanghai');
function usage(){
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
<header>
<title></title>
</header>
<body>
 Hi From #CLASS template!
</body>
</html>';
$m_temp = '
<?php
class #CLASS extends Afx_Module_Abstract
{
    protected $_tablename = "t_#TABLE";
    public static $_instance = NULL;
    protected function __construct ()
    {
        parent::__construct();
    }
    /**
     * @return #CLASS
     */
    public static function Instance ()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
';
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
        $class_file = $cwd . "/application/controllers/" . $class . ".php";
        $view_path = $cwd . "/application/views/" . $class;
        if (file_exists($class_file)) {
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
        $class_file = $cwd . "/application/models/" . $class . ".php";
        $class_temp = str_replace('#TABLE', strtolower($class), $m_temp);
        $class_temp = str_replace('#CLASS', $class, $class_temp);
        echo "class file=$class_file\nclass template=$class_temp";
        echo "write class file\n";
        file_put_contents($class_file, $class_temp);
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
$path . '/application/library/Afx', $path . '/application/library/Afx/Db', 
$path . '/application/library/Afx/Module', $path . '/application/models', 
$path . '/application/plugins'), 
'files' => array(
array('name' => $path . '/index.php', 
'content' => '<?php
date_default_timezone_set("Asia/Shanghai");
define("APP_PATH",  $_SERVER["DOCUMENT_ROOT"]);
if(file_exists(APP_PATH."/conf/auto.php")){
  require_once APP_PATH."/conf/auto.php";
}
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini", "production");
$app->bootstrap()->run();'), 
array('name' => $path . '/.htaccess', 
'content' => 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* /index.php'), 
array('name' => $path . '/conf/application.ini', 
'content' => '[production]
application.directory=APP_PATH "/application
application.dispatcher.catchException=TRUE
application.use_spl_autoload=1
'), 
array('name' => $path . '/conf/auto.php', 
'content' => '<?php
$root = $_SERVER["DOCUMENT_ROOT"] . "/application";
$load_paths = "$root/models;$root/modules;$root/plugins;$root/library";
function __autoload ($class_name)
{
    ob_clean();
    global $load_paths;
    $paths = explode(";", $load_paths);
    if (strstr($class_name, "_")) {
        $class_name = str_ireplace("_", "/", $class_name);
    }
    if (is_array($paths)) {
        foreach ($paths as $path) {
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
  'master'=>array('host'=>'127.0.0.1','port'=>'3306','user'=>'root','password'=>'','dbname'=>'pdcp','charset'=>'utf8'),
  'slave'=>array('host'=>'127.0.0.1','port'=>'3306','user'=>'root','password'=>'','dbname'=>'pdcp','charset'=>'utf8'),
  ),
  'memcache'=>array(
    'type'=>'memcache',
    'master'=>array('host'=>'192.168.188.73','port'=>'11211'),
    'slave'=>array('host'=>'192.168.188.73','port'=>'11213')
  ),
  'mongo'=>array(
   'type'=>'mongo',
   'master'=>array('host'=>'127.0.0.1','port'=>'27017'),
   'slave'=>array('host'=>'127.0.0.1','port'=>'27017')
  )
  
);"), 
array('name' => $path . '/application/Bootstrap.php', 
'content' => '<?php 
<?php 
class Bootstrap  extends Yaf_Bootstrap_Abstract{
    public function _initDb(){
        if(file_exists("conf/conf.php"))  {  
        $conf=include_once"conf/conf.php";
        Afx_Db_Adapter::initOption($conf);
        Afx_Db_Memcache::initOption($conf);
        $mmc=Afx_Db_Memcache::Instance();
        spl_autoload_unregister(array("Yaf_Loader", "autoload"));
        spl_autoload_register("__autoload");
        }
    }
    public function _initModel(){
        session_start();
        ob_start();
    }
}
 '), 
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
array('name' => $path . '/application/views/error/index.phtml', 
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
            file_put_contents($k['name'], $data);
        }
    }
}
if (file_exists('Adapter.php') &&
 file_exists($path . '/application/library/Afx/Db/')) {
    echo 'cpoy Adapter.php', "\n";
    copy('Adapter.php', $path . '/application/library/Afx/Db/Adapter.php');
    unlink('Adapter.php');
}
if (file_exists('Abstract.php') &&
 file_exists($path . '/application/library/Afx/Module/')) {
    echo 'cpoy Abstract.php', "\n";
    copy('Abstract.php', $path . '/application/library/Afx/Module/Abstract.php');
    unlink('Abstract.php');
}
if (file_exists('Memcache.php') &&
 file_exists($path . '/application/library/Afx/Db/')) {
    echo 'cpoy Memcache.php', "\n";
    copy('Memcache.php', $path . '/application/library/Afx/Db/Memcache.php');
    unlink('Memcache.php');
}
if (file_exists('Exception.php') &&
 file_exists($path . '/application/library/Afx/Db/')) {
    echo 'cpoy Exception.php', "\n";
    copy('Exception.php', $path . '/application/library/Afx/Db/Exception.php');
    unlink('Exception.php');
}