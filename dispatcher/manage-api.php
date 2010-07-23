<?php
require_once('db.php');

if ( $_REQUEST['action'] == 'add' ) {
	$db->query('INSERT INTO clients (client, description) VALUES ("' . $_REQUEST['client'] . '", "' . $_REQUEST['description'] . '")');
} else {
	$results = $db->query('SELECT * FROM clients');
	while ($row = $results->fetchArray()) {
	    var_dump($row);
	}
}