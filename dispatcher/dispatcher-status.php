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
$agents = $db->query('SELECT * FROM agents ORDER BY agent DESC');
$tmp = array();
while ( $agent = $agents->fetchArray() ) {
	$tmp[$agent['id']] = $agent['agent'];
}
$agents = $tmp;

$jobs = $db->query('SELECT * FROM jobs ORDER BY created DESC');
while ( $job = $jobs->fetchArray() ) {
	echo 'JOB: ' . $job['id'] . "\t" . $job['status'] . "\n";
	$tasks = $db->query('SELECT * FROM job_tasks WHERE job_id=' . $job['id']);
	while ( $task = $tasks->fetchArray() ) {
		echo "\t" . $agents[$task['agent_id']] . '(' . $task['agent_id'] . ")\t" . $task['status'] . "\t" . $task['task'] . "\t" . str_replace("\n", '\n', $task['arguments']) . "\n";
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
