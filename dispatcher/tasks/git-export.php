<?php

function task_git_deploye($args) {
	// Determine path to git
	$git = trim(`which git`);
	echo "INFO: Found git binary $git\n";
	
	$fp = popen($git, 'r');
	if ( $fp ) {
		echo '';
	}
	fclose($fp);
	return true;
}
