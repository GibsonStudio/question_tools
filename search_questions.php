<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

global $DB, $CFG;

$PAGE->requires->js('/blocks/question_tools/javascript/jquery-1.10.2.min.js');
$PAGE->requires->js('/blocks/question_tools/javascript/question_tools.js');
			
$PAGE->set_url('/blocks/question_tools/search_questions.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarsearch', 'block_question_tools')); 
$editnode->make_active();

$search_string = isset($_POST['search_string']) ? $_POST['search_string'] : '';

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('searchheading', 'block_question_tools').'</div>';

echo '<table class="qt_table"><tr>';
echo '<td class="qt_search_heading">Search:</td>';
echo '<td><input style="width:300px;" type="text" id="search_string" value="'.$search_string.'" autocomplete="off" /></td>';
echo '<td class="qt_search_heading"><button onclick="search_questions(\''.$CFG->wwwroot.'\');">Search</button></td>';
echo '</tr></table>';

echo '<hr />';

echo '<div id="qt_output">';

echo '</div>';

echo $OUTPUT->footer();

?>