
Task execution chain:

Tasks 2 and 3 below will wait until task 1 has completed with success

Id	Task	Host		Depends On		Args
1	echo	host-a						{"valid":"json"}
2	echo	host-b		1				{"say":"something"}
3	echo	host-c		1				{"say":"Another host"}
