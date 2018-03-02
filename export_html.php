<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/export_text.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarviewbycategory', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$categoryid = isset($_GET['categoryid']) ? $_GET['categoryid'] : 0;

echo '<div class="qt_page_heading">Export as HTML</div>';

$category_name = $DB->get_field('question_categories', 'name', array('id'=>$categoryid));
$questionids = qt_get_questions_in_category($categoryid);

echo '<h4>Questions in '.$category_name.'</h4>';

echo '<div style="width: 90%; height: 600px; border:1px solid #333333; overflow: auto; padding: 10px;">';

foreach ($questionids as $questionid)
{
	echo qt_get_question_as_html($questionid);
	echo "<br /><br />";
}

echo '</div>';

echo $OUTPUT->footer();

?>