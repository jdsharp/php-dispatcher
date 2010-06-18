<?php

unlink('manage.db');
$db = new SQLite3('manage.db');

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
	description TEXT
);
');

$db->query('
INSERT INTO task_types (task, description) VALUES ("echo", "Test echo service");
INSERT INTO task_types (task, description) VALUES ("svn-export", "Exports a svn repository to a local directory");
');

$db->query('
CREATE TABLE batch (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	description TEXT
);
');

$db->query('
INSERT INTO batch (description) VALUES ("First batch job");
');

$db->query('
CREATE TABLE batch_tasks (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	batch_id INTEGER,
	agent_id INTEGER,
	sort_order INTEGER,
	requires_batch_task_id INTEGER,
	task_type_id INTEGER,
	arguments TEXT
);
');

$db->query('
INSERT INTO batch_tasks (batch_id, agent_id, sort_order, task_type_id, arguments) VALUES (1, 1, 1, 1, \'{"say":"Hello world!"}\');
INSERT INTO batch_tasks (batch_id, agent_id, sort_order, requires_batch_task_id, task_type_id, arguments) VALUES (1, 1, 2, 1, 1, \'{"say":"End of task!"}\');
');

$db->query('
CREATE TABLE jobs (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	updated INTEGER,
	batch_id INTEGER,
	status VARCHAR(32)
);
');

$db->query('
CREATE TABLE job_tasks (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	updated INTEGER,
	job_id INTEGER,
	agent_id INTEGER,
	require_job_task_id INTEGER,
	task VARCHAR(255),
	arguments TEXT,
	status VARCHAR(32),
	batch_task_id INTEGER
);
');

$db->query('
INSERT INTO jobs (created, batch_id, status) VALUES (' . time() . ', 1, "QUEUED");
INSERT INTO job_tasks (created, job_id, task, agent_id, arguments, status, batch_task_id) VALUES (' . time() . ', 1, "echo", 1, \'{"say":"Hello world!"}\', "QUEUED", 1);
INSERT INTO job_tasks (created, job_id, task, require_job_task_id, agent_id, arguments, status, batch_task_id) VALUES (' . time() . ', 1, "echo", 1, 2, \'{"say":"End of task!"}\', "QUEUED", 2);
');

$db->query('
CREATE TABLE job_task_log (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	created INTEGER,
	job_task_id INTEGER,
	status VARCHAR(32),
	runtime NUMERIC,
	message TEXT
);
');
