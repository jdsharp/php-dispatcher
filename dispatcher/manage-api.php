<?php
require_once('db.php');

if ( $_REQUEST['action'] == 'list-agents' ) {
	$result = $db->query('SELECT * FROM agents ORDER BY agent DESC');
	$agents = array();
	while ( $tmp = $result->fetchArray() ) {
		$agents[] = array(
			'id' => $tmp['id'],
			'agent' => $tmp['agent'],
			'description' => $tmp['description']
		);
	}
	echo '{"status":"success","data":';
	echo json_encode($agents);
	echo '}';
	exit;
} else if ( $_REQUEST['action'] == 'list-tasks' ) {
	$result = $db->query('SELECT * FROM task_types ORDER BY task DESC');
	$agents = array();
	while ( $tmp = $result->fetchArray() ) {
		$agents[] = array(
			'id' => $tmp['id'],
			'task' => $tmp['task'],
			'description' => $tmp['description']
		);
	}
	echo '{"status":"success","data":';
	echo json_encode($agents);
	echo '}';
	exit;
} else if ( $_REQUEST['action'] == 'add' ) {
	$db->query('INSERT INTO clients (client, description) VALUES ("' . $_REQUEST['client'] . '", "' . $_REQUEST['description'] . '")');
} else {
	$results = $db->query('SELECT * FROM clients');
	while ($row = $results->fetchArray()) {
	    var_dump($row);
	}
}
