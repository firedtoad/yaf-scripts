<?php
/**
 * Yaf_View_Interface
 * @since 2.0
*/

interface  Yaf_View_Interface{
 public  function assign($key,$value=NULL);
 public  function render($tpl,$vars=array());//注意第二个参数是数组 官方手册上写的不对
 public  function display($tpl,$vars=array());//注意第二个参数是数组 官方手册上写的不对
 public function setScriptPath($template_dir);//注意该函数最好不要写任何内容 Yaf 会把默认的viewPath带过来我们不用默认的viewPath
}