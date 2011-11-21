<?php
date_default_timezone_set('Asia/Shanghai');
$options=array();
$template='<?php
class #CLASSController extends Yaf_Controller_abstract {
   public function indexAction() {
   	 echo "Hi from #CLASS action";
   }
}
';
$view_temp='<html>
<header>
<title></title>
</header>
<body>
 Hi From #CLASS template!
</body>
</html>';
function parse_command_line($str){
	global $options;
	$arr=explode('=', $str);
	if(isset($arr[0])&&isset($arr[1])){
		$options[$arr[0]]=$arr[1];
	}
}

if(!isset($argv)||!is_array($argv))exit( "no argv specific\n");
array_map('parse_command_line', $argv);
$cwd=getcwd();
$lock_file=$cwd."/.yaf_lock";

if(file_exists($lock_file)){
     if(isset($options['c'])||isset($options['-c'])){
        if(empty($options['c'])&&empty($options['-c'])){
             exit("controller is missing");
         }
         $class=(isset($options['c'])?$options['c']:$options['-c']);
         $class_file=$cwd."/application/controllers/".$class.".php";
         $view_path=$cwd."/application/views/".$class;
         $class_temp=str_replace('#CLASS', $class, $template);
         $view_temp=str_replace('#CLASS', $class, $view_temp);
         echo "class file=$class_file\nclass template=$class_temp";
         echo "write class file\n";
         file_put_contents($class_file, $class_temp);
         if(!file_exists($view_path)){
             echo "mkdir $view_path\n";
             mkdir($view_path,777,TRUE);
         }
         $view_file=$view_path."/index.phtml";
         echo "write view file\n";
         file_put_contents($view_file, $view_temp);
         exit("done\n");
     }  
     exit("no op\n");
}
if(!isset($options['path']))exit( "no path specific\n");

$path=(isset($options['path'])?$options['path']:dirname(__FILE__));
if(!file_exists($path)){
	mkdir($path,0777,TRUE);
}
$path=realpath($path);
$lock_file=$path."/.yaf_lock";
$conf=array(
'path'=>'./',
'paths'=>array(
 $path.'/conf',
 $path.'/application',
 $path.'/application/controllers',
 $path.'/application/views',
 $path.'/application/views/index',
 $path.'/application/modules',
 $path.'/application/library',
 $path.'/application/library/Afx',
 $path.'/application/library/Afx/Db',
 $path.'/application/library/Afx/Module',
 $path.'/application/models',
 $path.'/application/plugins'
 ),
'files'=>array(
 array('name'=>$path.'/index.php','content'=>'<?php
define("APP_PATH",  $_SERVER["DOCUMENT_ROOT"]);
if(file_exists(APP_PATH."/conf/auto.php")){
  require_once APP_PATH."/conf/auto.php";
}
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini", "production");
$app->bootstrap()->run();'),
 array('name'=>$path.'/.htaccess','content'=>'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* /index.php'),
 array('name'=>$path.'/conf/application.ini','content'=>'[production]
application.directory=APP_PATH "/application
application.dispatcher.catchException=TRUE

'),
array('name'=>$path.'/conf/auto.php','content'=>'<?php
$root = $_SERVER["DOCUMENT_ROOT"] . "/application";
$load_paths = "$root/models;$root/modules;$root/plugins;$root/library";
function __autoload ($class_name)
{
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
array('name'=>$path.'/conf/conf.php','content'=>"<?php
<?php
return array(
  'db'=>array(
  'type'=>'mysql',
  'master'=>array('host'=>'127.0.0.1','port'=>'3306','user'=>'root','password'=>'','dbname'=>'pdcp','charset'=>'utf8'),
  'slave'=>array('host'=>'127.0.0.1','port'=>'3306','user'=>'root','password'=>'','dbname'=>'pdcp','charset'=>'utf8'),
  ),
);"),
 array('name'=>$path.'/application/Bootstrap.php','content'=>'<?php 
class Bootstrap  extends Yaf_Bootstrap_Abstract{
    public function _initDb(){
        if(file_exists("conf/conf.php"))  {  
        $conf=include_once  "conf/conf.php";
        Afx_Db_Adapter::initOption($conf);
        }
    }
    public function _initModel(){
    }
}
 '),
 array('name'=>$path.'/application/controllers/Index.php','content'=>'<?php
class IndexController extends Yaf_Controller_abstract {
   public function indexAction() {
   	 echo "Hi from yaf";
   }
}
 '),
array('name'=>$path.'/application/controllers/Error.php','content'=>'<?php
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
 array('name'=>$path.'/application/views/index/index.phtml','content'=>'<html>
 <head>
   <title>Hello World</title>
 </head>
 <body>
    Hellow World!
 </body>
</html>'),
)
);
if(!file_exists($path)){
	mkdir($path,0777,TRUE);
}
if(is_array($conf['paths'])){
foreach($conf['paths'] as &$k){
	if(!file_exists($k)){
		echo "make dir  $k"."\n";
		mkdir($k);
	}
  }
}
if(is_array($conf['files'])){
   if(!file_exists($lock_file)){
       file_put_contents($lock_file, 'yaf_lock_file');
   }
foreach ($conf['files'] as &$k){
    if(isset($k['name'])){
    	echo  'create file  ' , $k['name']."\n";
    	$data=isset($k['content'])?$k['content']:'';
    	file_put_contents($k['name'], $data);
    }
  }
}
if(file_exists('Adapter.php')&&file_exists($path.'/application/library/Afx/Db/')){
    file_put_contents($path.'/application/library/Afx/Db/Adapter.php', file_get_contents('Adapter.php'));
}
if(file_exists('Abstract.php')&&file_exists($path.'/application/library/Afx/Module/')){
    file_put_contents($path.'/application/library/Afx/Module/Abstract.php', file_get_contents('Abstract.php'));
}