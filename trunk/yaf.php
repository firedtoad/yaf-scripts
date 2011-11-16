<?php
date_default_timezone_set('Asia/Shanghai');
$options = array();
$template = '<?php
class #CLASSController extends Yaf_Controller_abstract {
   public function indexAction() {
   	 echo "Hi from #CLASS action";
   }
}
';
$view_temp = '
<html>
<header>
<title></title>
</header>
<body>
 Hi From #CLASS template!
</body>
</html>';
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
    exit("no op\n");
}
if (! isset($options['path']))
    exit("no path specific\n");
$path = (isset($options['path']) ? $options['path'] : dirname(__FILE__));
if (! file_exists($path)) {
    mkdir($path, 0777, TRUE);
}
$path = realpath($path);
$lock_file = $path . "/.yaf_lock";
$conf = array('path' => './', 
'paths' => array($path . '/conf', $path . '/application', 
$path . '/application/controllers', $path . '/application/views', 
$path . '/application/views/index', $path . '/application/modules', 
$path . '/application/library', $path . '/application/models', 
$path . '/application/plugins'), 
'files' => array(
array('name' => $path . '/index.php', 
'content' => '<?php
define("APP_PATH",  $_SERVER["DOCUMENT_ROOT"]);
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini", "production");
$app->run();'), 
array('name' => $path . '/.htaccess', 
'content' => 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* /index.php'), 
array('name' => $path . '/conf/application.ini', 
'content' => '[production]
application.directory=APP_PATH "/application"
 '), array('name' => $path . '/application/Booststrap.php', 'content' => ''), 
array('name' => $path . '/application/controllers/Index.php', 
'content' => '<?php
class IndexController extends Yaf_Controller_abstract {
   public function indexAction() {
   	 echo "Hi from yaf";
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
</html>')));
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