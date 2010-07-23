<?php
require_once('auth.php');
require_once('site.header.php');
?>
<div id="nav">
	<ul>
		<li><a href="#agents" class="action-agents">Agents</a></li>
		<li><a href="#taskTypes" class="action-tasks">Task Types</a></li>
		<li><a href="#batchJobs" class="action-batch">Define Jobs</a></li>
		<li><a href="#jobs" class="action-status">Dispatcher Status</a></li>
	</ul>
</div>
<div id="content">
	<div class="agents">
		<a href="#" class="action-add-agent">+ Add Agent</a>
		<table class="agents"></table>
	</div>
	<div class="tasks">
		<a href="#" class="action-add-task">+ Add Task</a>
		<table class="tasks"></table>
	</div>
</div>


<?php
require_once('site.footer.php');
