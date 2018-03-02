function search_questions (my_path) { 
	
	val = $("#search_string").val();
		
	if (val.length >= 1)
	{
	
		$('#qt_output').html('Searching....<br /><br />');
		
		$.ajax({
			url: my_path + '/blocks/question_tools/search.php',
			type: 'POST',
			data: {search_string:val}
		}).error(function (e1, e2, e3) {
		
			$('#qt_output').html('<b>ERROR:</b> ' + e1 + ': ' + e2 + ': ' + e3);
			
		}).done(function (data) {
		
			$('#qt_output').html(data);
			
		});
	
	}
	
}

