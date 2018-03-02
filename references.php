<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/references.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarreferences', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$categoryid = isset($_POST['categoryid']) ? $_POST['categoryid'] : 0;
$ref = isset($_POST['ref']) ? trim($_POST['ref']) : '';

//set default values
$show_choose_category = true;
$show_add_ref_form = false;
$add_ref = false;
$show_questions = false;
$show_blank_ref_warning = false;


if (isset($_POST['submit'])) {
	$show_questions = true;
	$show_add_ref_form = true;
}


if (isset($_POST['submit_ref'])) {
	
	if ($ref == '') {
		$show_blank_ref_warning = true;
		$show_add_ref_form = true;
		$show_questions = true;
	} else {
		$add_ref = true;
		$show_choose_category = false;
	}
	
}

echo '<div class="qt_page_heading">'.get_string('referencesheading', 'block_question_tools').'</div>';

if ($show_choose_category) {
	
	echo '<form action="" method="POST">';
	echo qt_get_category_select($categoryid);
	echo '<input type="submit" name="submit" value="Show Questions" />';
	echo '</form>';

}





if ($add_ref) {
		
	//add refs to questions in categoryid
	$category_name = $DB->get_field('question_categories', 'name', array('id'=>$categoryid));
	echo '<b>Adding ref "'.$ref.'" to category '.$category_name.'....</b><hr />';
	
	$questionids = qt_get_questions_in_category($categoryid);
	$ref_number = 1;
	
	foreach ($questionids as $questionid)
	{
		
		$question_name = $DB->get_field('question', 'name', array('id'=>$questionid));
		$split_name = split(';', $question_name); 
		
		if (count($split_name) > 1) {
			$question_name = $split_name[1];
		} 
		
		$question_text = $DB->get_field('question', 'questiontext', array('id'=>$questionid));
		
		$new_name = $ref.$ref_number.';'.substr($question_text, 0, 29);
		
		echo 'Changing question '.$questionid.' name to "'.$new_name.'"....<br/>';
		
		$data = new stdClass();
		$data->id = $questionid;
		$data->name = $new_name;
		$updateok = $DB->update_record('question', $data);
		
		if ($updateok) {
			echo 'Ref added OK.';
		}
				
		echo '<hr />';
		$ref_number++;
		ob_flush();
		
	}
	
	echo '<b>Finished!</b><a href=""><button>Add Another Ref</button></a>';	
	
} 




if ($show_blank_ref_warning) {
	echo '<div class="qt_error">Ref must not be blank!</div>';
}






if ($show_add_ref_form) {
	
	//show add / change ref form
	echo '<form action="" method="POST">';
	echo '<table class="qt_table"><tr>';
	echo '<td class="qt_search_heading">Ref:</td>';
	echo '<td><input type="text" name="ref" value="'.$ref.'" /></td>';
	echo '<input type="hidden" name="categoryid" value="'.$categoryid.'" />';
	echo '<td><input type="submit" name="submit_ref" value="Add / Change Ref" /></td>';
	echo '</tr></table>';
	echo '</form>';
		
	echo '<hr />';
	
}





if ($show_questions) {
	
	$category_name = $DB->get_field('question_categories', 'name', array('id'=>$categoryid));
	$questionids = qt_get_questions_in_category($categoryid);
	
	echo '<h4>Questions in '.$category_name.'</h4>';
	
	foreach ($questionids as $questionid)
	{
		echo qt_get_question($questionid);
	}
	
	echo '<hr />';
	
}



echo $OUTPUT->footer();

?>