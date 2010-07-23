$.ajaxSetup({
	url: '/~jdsharp/site-deploy/dispatcher/manage-api.php',
	type: 'POST',
	dataType: 'json'
});


function listAgents() {
	var $agents = $('#content')
		.children().hide()
		.filter('div.agents').show()
	$.ajax({
		data: {
			action: 'list-agents'
		},
		success: function(json) {
			if ( json.status == 'success' ) {
				var html = '';
				$.each(json.data, function() {
					html += '<tr>';
					html += '<td>' + this.agent + '</td>';
					html += '<td>' + this.description + '</td>';
					html += '</tr>';
				});
				$agents.find('table.agents').empty().append(html);
			}
		}
	});
	/*
	Epic.request('agents.list', function() {
		
	});
	*/
}

$('.action-agents').click(listAgents);


$(document).delegate('.action-tasks', 'click', function() {
	var $tasks = $('#content')
		.children().hide()
		.filter('div.tasks').show();
	$.ajax({
		data: {
			action: 'list-tasks'
		},
		success: function(json) {
			if ( json.status == 'success' ) {
				var html = '';
				$.each(json.data, function() {
					html += '<tr>';
					html += '<td>' + this.task + '</td>';
					html += '<td>' + this.description + '</td>';
					html += '</tr>';
				});
				$tasks.find('table.tasks').empty().append(html);
			}
		}
	});
});
