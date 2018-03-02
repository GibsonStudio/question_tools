<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/show_where_used.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarviewbycategory', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$categoryid = isset($_POST['categoryid']) ? $_POST['categoryid'] : 0;

echo '<div class="qt_page_heading">'.get_string('viewbycategoryheading', 'block_question_tools').'</div>';

echo '<form action="" method="POST">';
echo qt_get_category_select($categoryid);
echo '<input type="submit" name="submit" value="Show Questions" />';
echo '</form>';

if (isset($_POST['submit']))
{
	
	echo '<hr />';
	
	
	$category_name = $DB->get_field('question_categories', 'name', array('id'=>$categoryid));
	$questionids = qt_get_questions_in_category($categoryid);
	
	echo '<h4>Questions in '.$category_name.'</h4>';
	
	foreach ($questionids as $questionid)
	{
		echo qt_get_question($questionid);
	}
	
	
	
	
	
	
	
}

echo $OUTPUT->footer();

?>