# Afx Framework-----Extension Yaf By Brother Bird #
> ## Add database module and view layers ##

---

## **Introduction** ##
> An easy and faster framework for mvc

---

## **Installtion** ##
> you need to install the yaf extension for php see http://code.google.com/p/yafphp/

---

# Gernerate Project #
```
root@ubuntu:/tmp/project#php yaf.php path=.
root@ubuntu:/tmp/project# ll
drwxr-xr-x  8 root root  4096 2012-03-16 19:54 application/
drwxr-xr-x  2 root root  4096 2012-03-16 19:54 conf/
-rw-r--r--  1 root root   174 2012-03-16 19:54 .htaccess
-rw-r--r--  1 root root   784 2012-03-16 19:54 index.php
drwxr-xr-x  2 root root  4096 2012-03-16 19:54 Public/
-rw-r--r--  1 root root    13 2012-03-16 19:54 .yaf_lock
-rw-r--r--  1 root root 21150 2012-02-27 17:09 yaf.php
```

---

## Gernerate Controller ##
```
root@ubuntu:/tmp/project# php yaf.php -c=MyController
root@ubuntu:/tmp/project# ll application/controllers/mycontroller.php 
-rw-r--r-- 1 root root 154 2012-03-16 20:04 application/controllers/mycontroller.php
```

---

## Gernerate Action ##
```
root@ubuntu:/tmp/project# php yaf.php -c=MyController -a=Add 
root@ubuntu:/tmp/project# vim application/controllers/mycontroller.php  
 <?php
  2 class MycontrollerController extends Yaf_Controller_abstract {
  3    public function indexAction() {
  4        echo "Hi from Mycontroller action";
  5    }
  6    public function AddAction(){
  7    }
  8 }
```

---

## Gernerate Model ##
```
root@ubuntu:/tmp/project# php yaf.php -m=Student 
root@ubuntu:/tmp/project# vim application/models/Student.php   
  1 
  2 <?php
  3 class Student extends Afx_Module_Abstract
  4 {
  5     protected $_tablename = 'Student';
  6     /**
  7     * @var Student $_instance
  8     */
  9     protected static $_instance = NULL;
 10 
 11     /**
 12      * @return  Student
 13      */
 14     public function __construct ()
 15     {
 16     }
 17     /**
 18      * @return Student
 19      */
 20     public static function Instance ()
 21     {
 22         if (NULL === self::$_instance) {
 23             self::$_instance = new self();
 24         }
 25         return self::$_instance;
 26     }
 27 }
~            
```

---

## Op ##
```
$student=Student::Instance();
$student->name="firedtoad";
$student->age=23;
$student->sex=1;
$student->save();
```

---

## Memcache ##
```
$mm=Afx_Db_Memcache::Instance();
$mm->set('student',Afx_Module::toArray($student));
$s=$mm->get('student');
Afx_Debug_Helper::print_r($s);
```
