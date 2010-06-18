<?php

function task_deploy_site($args) {
	echo "ARGS Received:\n";
	print_r($args);
	echo "\n";
	
	// Do something to deploy the site
	echo "Deploy site task running\n";
	
	if ( false ) {
		throw new TaskError("This task encountered an error");
	}
	$i = 500000;
	while ( $i-- > 0 ) { };
	return true;
}