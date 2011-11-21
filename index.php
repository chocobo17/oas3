
<html>
	<head>
		<title>oas 周月報系統 3.0</title>
	</head>
<?php
/**
 * 2011-11-10
 * @author k1
 * 
 * index.php 入口程式
 * 主要修改：
 * 1. APPLICATION_ENV: 影響套用application.ini內的區塊，若正式上線使用online，否則使用devel。
 * 2. APPLICATION_SESSION: 可任意設定，通常設定專案程式英文名稱。
 */
//test

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'devel') );

//設定此應用系統SESSION名稱（通常用專案名）
defined('APPLICATION_SESSION') || define('APPLICATION_SESSION', 'OAS3');

//Ensure Library/ is on include_path
set_include_path( implode( PATH_SEPARATOR, array(
									      	realpath(APPLICATION_PATH . '/../library'),
									      	get_include_path(),
										   )
				  )
				);

/** Zend_Application **/
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();
?>
</html>