<?php

function task_svn_export($args) {
	// Determine path to SVN
	$svn = 	trim(`which svn`);
	echo "INFO: Found svn binary $svn\n";
	
	$fp = popen($svn, 'r');
	if ( $fp ) {
		echo '';
	}
	fclose($fp);
	return true;
}
