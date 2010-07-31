<?php
/**
 * This file provides the raw PHP source for defining a task and 
 * providing the source to an agent
 */
header('Content-type: text/plain');
define('TASKS_DIR', dirname(__FILE__) . '/tasks/' );
$task = preg_replace('[^a-z0-9_]', '', $_REQUEST['task']);
if ( file_exists(TASKS_DIR . '/' . $task . '.php') ) {
	echo file_get_contents(TASKS_DIR . '/' . $task . '.php');
	exit;
}
echo '404';
