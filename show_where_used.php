<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/show_where_used.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarshowwhereused', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('showwhereusedheading', 'block_question_tools').'</div>';

$questionid = isset($_REQUEST['questionid']) ? $_REQUEST['questionid'] : '';

echo '<form action="" method="POST">';
echo '<table class="qt_table"><tr>';
echo '<td class="qt_search_heading">Question ID:</td>';
echo '<td><input type="text" name="questionid" value="'.$questionid.'" /></td>';
echo '<td><input type="submit" name="submit" value="Show Where Used" /></td>';
echo '</tr></table>';
echo '</form>';

echo '<hr />';

if (isset($_REQUEST['submit']))
{
	
	if (empty($DB->get_field('question', 'category', array('id'=>$questionid)))) {
		
		//question does not exist
		echo '<div class="qt_error">There is no question with an id of '.$questionid.'.</div>';
		
		
	} else {

		//show question
		echo qt_get_question($questionid).'<hr />';
		
		//show where specifically used

		$quizids = qt_get_question_specifically_used($questionid);
		
		if (count($quizids) > 0) {			
			echo qt_get_used_in_table(array('title'=>'Specifically used in....', 'id_array'=>$quizids));			
		}
		
		
		//show where could be randomly picked
		echo '<hr />';

		$quizids = qt_get_question_randomly_used($questionid);
		
		if (count($quizids) > 0) {			
			echo qt_get_used_in_table(array('title'=>'Could be randomly used in....', 'id_array'=>$quizids));			
		}
		
		
	
	}
	
}



echo $OUTPUT->footer();

?>