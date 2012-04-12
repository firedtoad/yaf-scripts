yaf.php 脚本使用方法
php yaf.php [,选项=选项值]
选项
path=pathdir   项目目录
-c=name        控制器名
-m=name        模型名称
-a=name        动作名
例子
php yaf.php path=. 在当前目录下创建项目 ,linux下请注意目录权限
php yaf.php -c=Goods 添加一个控制器名为Goods,必须在已经创建项目目录下执行
php yaf.php -m=Goods 添加一个模型名为Goods,必须在已经创建项目目录下执行
php yaf.php -c=Goods -a=Add 为控制器Goods添加一个动作Add,必须在已经创建项目目录下执行,注意 -a 必须和-c同时使用