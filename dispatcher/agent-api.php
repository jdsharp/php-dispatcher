<?php
switch ( $_REQUEST['action'] ) {
	case 'tasks':
		require_once('action-tasks.php');
		break;
	case 'tasksource':
		require_once('action-tasksource.php');
		break;
	case 'status':
		require_once('action-status.php');
		break;
	default: 
		header('Content-type: text/plain');
		echo '{"status":"error", "message":"Action unknown"}';
}
