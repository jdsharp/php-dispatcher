<?php

function task_echo($args) {
	if ( $args->say ) {
		echo "Echo says '{$args->say}'\n";
	} else {
		echo "Echo is silent\n";
	}
	echo "ARGS Received:\n";
	print_r($args);
	echo "\n";
	return true;
}