<?php

// This is the process which is requesting the deploy target
$agent	 	= $_REQUEST['for'];
$secret 	= 'some-secret-key-that-is-long';
$salt		= date('i');
$hash		= md5( $secret . $salt );

header('Content-type: text/plain');

if ( false && $hash !== $_REQUEST['hash'] ) {
	echo '{"status":"error","message":"Hash failure"}';
	exit;
}

require_once('db.php');
$agentId = $db->querySingle('SELECT id FROM agents WHERE agent="' . $agent . '"');
if ( !$agentId ) {
	echo '{ "status" : "error", "message" : "Agent ' . $agent . ' not found" }';
	exit;
}

$jobTasks = $db->query('SELECT * FROM jobs,job_tasks WHERE job_tasks.agent_id=' . $agentId . ' AND job_tasks.job_id=jobs.id AND job_tasks.status="QUEUED"');
$tasks = array();
while ( $row = $jobTasks->fetchArray() ) {
	// If we have a dependent task, make sure it successfully completed before we run this task, otherwise ignore
	if ( !empty($row['require_job_task_id']) ) {
		$status = $db->querySingle('SELECT status FROM job_tasks WHERE id=' . $row['require_job_task_id']);
		if ( $status != 'SUCCESS' ) {
			continue;
		}
	}
	
	// Check our overall job status
	$status = $db->querySingle('SELECT status FROM jobs WHERE id=' . $row['job_id']);
	if ( $status != 'QUEUED' && $status != 'RUNNING' ) {
		continue;
	}
	$task = new StdClass();
	$task->id 		= $row['id'];
	$task->job_id 	= $row['job_id'];
	$task->created 	= $row['created'];
	$task->updated 	= $row['updated'];
	$task->status 	= $row['status'];
	$task->task 	= $row['task'];
	$task->args 	= $row['arguments'];
	$tasks[] = $task;
}

?>
{ 	"status" : "success",
	"client" : "<?php echo $_REQUEST['for']?>",
	"tasks" : <?php echo json_encode($tasks)?>
}