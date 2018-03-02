<?php
 
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/lib/questionlib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/images.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarcustomquery', 'block_question_tools')); 
$editnode->make_active();

$query_string = isset($_POST['query_string']) ? $_POST['query_string'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('customqueryheading', 'block_question_tools').'</div>';

echo '<form action="" method="POST">';
echo '<table class="qt_table">';
echo '<tr><td class="qt_search_heading">Password:</td>';
echo '<td><input type="password" name="password" value="'.$password.'" /></td></tr>';
echo '<tr><td class="qt_search_heading" colspan="2">SELECT id FROM '.$CFG->prefix.'question....:</td></tr>';
echo '<tr><td colspan="2">';
echo '<textarea name="query_string" style="width: 500px;" rows="1">'.$query_string.'</textarea>';
echo '</td></tr>';
echo '<tr><td class="qt_search_heading" colspan="2"><input type="submit" name="submit" value="Run Query" /></td></tr>';
echo '</table>';
echo '</form>';

echo '<hr />';


if (isset($_POST['submit']))
{
	
	if (md5($password) == '66981bedb9102bc55c68ed9feac89c9f')
	{
	
		//remove any banned phrases
		$banned = array('UPDATE', 'DELETE', 'INSERT', 'CREATE', 'ALTER', 'MODIFY', 'DROP');
		
		foreach ($banned as $b) {
			$query_string = preg_replace('/'.$b.'\b/i', '', $query_string);
		}
		
		//run query
		
		$sql = 'SELECT {question}.id FROM {question} '.$query_string;
		echo '<b>Running query:</b> '.$query.'<hr />';
		
		$questions = $DB->get_recordset_sql($sql);
		
		foreach ($questions as $question)
		{
			echo qt_get_question($question->id);
		}
				
	} else {
		
		echo 'Incorrect password.';
		
	}
	
	
}





echo $OUTPUT->footer();

?>