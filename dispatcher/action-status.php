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
	$jobs = $db->query('SELECT * FROM jobs ORDER BY created DESC');
	while ( $job = $jobs->fetchArray() ) {
		echo 'JOB: ' . $job['id'] . "\t" . $job['status'] . "\n";
		$tasks = $db->query('SELECT * FROM job_tasks WHERE job_id=' . $job['id']);
		while ( $task = $tasks->fetchArray() ) {
			echo "\t" . $task['agent_id'] . "\t" . $task['status'] . "\t" . $task['task'] . "\t" . str_replace("\n", '\n', $task['arguments']) . "\n";
			$logs = $db->query('SELECT * FROM job_task_log WHERE job_task_id=' . $task['id']);
			while ( $log = $logs->fetchArray() ) {
				echo "\t\t" . $log['status'] . "\t" . $log['runtime'] . "\t" . str_replace("\n", '\n', $log['message']) . "\n";
			}
		}
	}
	
	/*
	$res = $db->query('SELECT * FROM jobs,batch_tasks,task_types WHERE jobs.batch_task_id=batch_tasks.id AND batch_tasks.task_type_id=task_types.id ORDER BY jobs.id DESC');
	while ( $row = $res->fetchArray() ) {
		echo $row['id'] . "\t" . $row['status'] . "\t" . $row['task'] . "\t" . $row['arguments'] . "\n";
		
		$status = $db->query('SELECT * FROM job_status WHERE job_id=' . $row['id']);
		while ( $row2 = $status->fetchArray() ) {
			echo "\t" . $row2['status'] . "\t" . $row2['runtime'] . "\t\n\t" . $row2['message'] . "\n---\n";
		}
	}
	
	echo "\n\n====\n\n";
	*/
//	$res = $db->query('SELECT * FROM job_status');
//	if ( $res->numColumns() && $res->columnType(0) != SQLITE3_NULL ) {
		
//	} else {
//		echo "No rows for job status\n";
//	}
}