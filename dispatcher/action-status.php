<?php
ini_set('display_errors', 1);
require_once('db.php');
/*
'for' 		=> $client,
'hash'		=> REQUEST_HASH,
'jobId' 	=> $jobId,
'processId'	=> PROCESS_ID,
'status'	=> $status,
'time' 		=> $time,
'message' 	=> $message
*/

/*
 * QUEUED, PAUSED, RUNNING, ERROR, SUCCESS
 */
header('Content-type: text/plain');

if ( isset($_REQUEST['job_task_id']) && isset($_REQUEST['status']) ) {
	if ( !isset($_REQUEST['runtime']) ) {
		$_REQUEST['runtime'] = -1;
	}
	if ( !isset($_REQUEST['message']) ) {
		$_REQUEST['message'] = '';
	}
	$query = 'INSERT INTO job_task_log (created, job_task_id, status, runtime, message) VALUES (' .
	time() . ', "' .
	SQLite3::escapeString($_REQUEST['job_task_id']) . '", "' . 
	SQLite3::escapeString($_REQUEST['status']) . '", "' .
	SQLite3::escapeString($_REQUEST['runtime']) . '", "' . 
	SQLite3::escapeString($_REQUEST['message']) . '");';
	$db->query($query);
	
	$db->query('UPDATE job_tasks SET status="' . $_REQUEST['status'] . '", updated=' . time() . ' WHERE id=' . $_REQUEST['job_task_id'] . '');
	
	// TODO: Update the parent jobs table when all tasks are complete
	
	echo '{"status":"success"}';
} else {
	echo '{"status":"error","message":"job_task_id and status not set"}';
}
