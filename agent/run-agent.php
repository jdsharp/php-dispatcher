<?php

if ( !isset($agent) ) {
	$agent = trim(`hostname`);
	// For debugging
	// $agent = 'test-a';
}
$secret	= 'some-secret-key-that-is-long';
$salt	= date('i');

// Set this to the URL which has the dispatcher Running
define('DISPATCHER_URL', 'http://localhost/~jdsharp/php-dispatcher/dispatcher/agent-api.php');

define('REQUEST_HASH', 	md5( $secret . $salt ) );
define('AGENT_ID',		$agent );
define('PROCESS_ID',	getmypid() );

define('LOCK_FILE', '/tmp/.run-agent-' . AGENT_ID . '.pid');

// -- No need to modify anything below here --
set_time_limit(0);
if ( file_exists(LOCK_FILE) ) {
	echo "INFO: Exit, another process (" . file_get_contents(LOCK_FILE) . ") is running\n";
	exit;
}

file_put_contents(LOCK_FILE, PROCESS_ID);
echo "INFO: Created lock file " . LOCK_FILE . "\n";
echo "INFO: Contacting dispatcher " . DISPATCHER_URL . "\n";

while ( $task = requestTask() ) {
	$fn = loadTaskDefinition($task->task);
	if ( $fn === false ) {
		echo "ERR: Failed to include task for '{$task->task}', exiting\n";
		break;
	}
	
	if ( !isset($task->args) ) {
		$task->args = "{}";
	}
	echo "INFO: RUN: $fn\n";
	updateStatus($task->id, 'RUNNING');
	try {
		ob_start();
		$start = microtime( true );
		$args = json_decode($task->args);
		$ret = $fn($args);
		$runtime = round(microtime(true) - $start, 3);
		$message = trim( ob_get_contents() );
		ob_end_clean();
		if ( $ret !== false ) {
			updateStatus($task->id, 'SUCCESS', $runtime, $message );
			echo "INFO: Task returned success\n";
		} else {
			updateStatus($task->id, 'ERROR', $runtime, $message );
			echo "INFO: Task returned error\n";
		}
	} catch (Exception $e) {
		$message = trim( ob_get_contents() );
		ob_end_clean();
		echo "INFO: Task returned an error: " . $e->getMessage() . "\n";
		updateStatus($task->id, 'ERROR', round(microtime(true) - $start, 3), 'Exception: ' . $e->getMessage() . "\n\nOutput:\n$message" );
	}
	
	break;
}
echo "INFO: Removing lock file\n";
unlink(LOCK_FILE);
exit;

// --- Client functions
function httpPost($url, $data) {
	echo "DEBUG: CURL url $url\n";
	print_r($data);
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL 			=> $url,
		CURLOPT_HEADER 			=> 0,
		CURLOPT_POST 			=> true,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_POSTFIELDS 		=> $data
	));
	$ret = curl_exec($ch);
	curl_close($ch);
	echo "RESPONSE: $ret\n";
	return $ret;
}

function updateStatus($taskId, $status, $runtime = -1, $message = '') {
	echo "$taskId ran in $runtime\n";
	$json = httpPost(DISPATCHER_URL, array(
		'action' 	=> 'status',
		'for' 		=> AGENT_ID,
		'hash'		=> REQUEST_HASH,
		'job_task_id' => $taskId,
		'process_id'=> PROCESS_ID,
		'status'	=> $status,
		'runtime' 	=> $runtime,
		'message' 	=> $message
	));
	return json_decode($json);
}
function requestTasks($client) {
	$json = httpPost(DISPATCHER_URL, array(
		'action' => 'tasks',
		'for' 	=> $client,
		'hash'	=> REQUEST_HASH
	));
	return json_decode($json);
}
function requestTask() {
	$payload = requestTasks(AGENT_ID);
	if ( $payload && $payload->status == 'success' ) {
		if ( is_array( $payload->tasks ) ) {
			// Only return the first task
			return array_shift($payload->tasks);
		}
	}
	return false;
}
function loadTaskDefinition($task) {
	$task = preg_replace('[^a-z0-9-]', '', $task);
	$task = str_replace('-', '_', $task);
	$fn = 'task_' . $task;
	if ( !function_exists($fn) ) {
		$taskSource = httpPost(DISPATCHER_URL, array(
			'action' => 'tasksource',
			'task' => $task
		));
		
		if ( $taskSource && $taskSource != '404' ) {
			$taskSource = preg_replace('/^(<\?(php)?)?(.*)(\?>)?$/m', '$3', $taskSource);
			echo "TaskSource: $taskSource\n";
			// Checks for syntax errors
			if ( @eval('return true; ' . $taskSource ) ) {
				//eval($taskSource);
				if ( function_exists($fn) ) {
					return $fn;
				}
				echo "ERR: Missing function definition for $fn\n";
				return false;
			}
			echo "ERR: Synatx error for task '$task'\n";
		}
		return false;
	}
	return $fn;
}

class TaskError extends Exception { }


