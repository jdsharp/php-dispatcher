<?php

unlink('manage.db');
$db = new SQLite3('manage.db');
chmod('manage.db', 0777);

$db->query('
CREATE TABLE agents (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	agent VARCHAR(255),
	description VARCHAR(255)
);
');

$db->query('
INSERT INTO agents (agent, description) VALUES ("test-a", "Test agent A");
INSERT INTO agents (agent, description) VALUES ("test-b", "Test agent B");
');

$db->query('
CREATE TABLE task_types (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	task VARCHAR(255),
	description TEXT,
	args TEXT
);
');

$db->query('
INSERT INTO task_types (task, description, args) VALUES ("echo", "Test echo service", "{\"say\":\"string\"}");
INSERT INTO task_types (task, description, args) VALUES ("svn-export", "Exports a svn repository to a local directory", "{\"svnRepo\":\"url\"}");
INSERT INTO task_types (task, description, args) VALUES ("git-export", "Exports a git repository to a local directory", "{\"gitRepo\":\"url\"}");
');

$db->query('
CREATE TABLE jobs (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	description TEXT
);
');

$db->query('
INSERT INTO jobs (description) VALUES ("First job definition");
');

$db->query('
CREATE TABLE job_tasks (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	job_id INTEGER,
	agent_id INTEGER,
	sort_order INTEGER,
	requires_job_task_id INTEGER,
	task_type_id INTEGER,
	arguments TEXT
);
');

$db->query('
INSERT INTO job_tasks (job_id, agent_id, sort_order, task_type_id, arguments) VALUES (1, 1, 1, 1, \'{"say":"Hello world!"}\');
INSERT INTO job_tasks (job_id, agent_id, sort_order, requires_job_task_id, task_type_id, arguments) VALUES (1, 1, 2, 1, 1, \'{"say":"End of task!"}\');
');

$db->query('
CREATE TABLE dispatches (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	updated INTEGER,
	job_id INTEGER,
	status VARCHAR(32)
);
');

$db->query('
CREATE TABLE dispatch_tasks (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	updated INTEGER,
	dispatch_id INTEGER,
	agent_id INTEGER,
	require_dispatch_task_id INTEGER,
	task VARCHAR(255),
	arguments TEXT,
	status VARCHAR(32),
	job_task_id INTEGER
);
');

$db->query('
INSERT INTO dispatches (created, job_id, status) VALUES (' . time() . ', 1, "QUEUED");
INSERT INTO dispatch_tasks (created, dispatch_id, task, agent_id, arguments, status, job_task_id) VALUES (' . time() . ', 1, "echo", 1, \'{"say":"Hello world!"}\', "QUEUED", 1);
INSERT INTO dispatch_tasks (created, dispatch_id, task, require_job_task_id, agent_id, arguments, status, job_task_id) VALUES (' . time() . ', 1, "echo", 1, 2, \'{"say":"End of task!"}\', "QUEUED", 2);
');

$db->query('
CREATE TABLE dispatch_task_log (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	dispatch_task_id INTEGER,
	status VARCHAR(32),
	runtime NUMERIC,
	message TEXT
);
');
