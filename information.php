<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/information.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarinformation', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('informationheading', 'block_question_tools').'</div>';

//category count

echo '<table class="qt_table" style="margin:10px;">';

echo '<tr><td class="qt_table_title">Categories:</td><td class="qt_table_data">'.qt_get_category_count().'</td>';
echo '<td class="qt_table_data"><b>Note:</b> This only counts created categories, it does not include default categories.</td></tr>';

echo '<tr><td class="qt_table_title">Categories with Questions:</td>';
echo '<td class="qt_table_data">'.qt_get_category_count(array('with_questions'=>true)).'</td>';
echo '<td class="qt_table_data"></td></tr>';

echo '<tr><td class="qt_table_title">Questions:</td>';
echo '<td class="qt_table_data">'.qt_get_total_question_count().'</td>';
echo '<td class="qt_table_data"></td></tr>';

echo '<tr><td class="qt_table_title">Questions with Images:</td>';
echo '<td class="qt_table_data">'.qt_get_question_count_with_images().'</td>';
echo '<td class="qt_table_data"><a href="images.php">View Questions</a></td></tr>';

echo '</table>';


//possible problems
echo '<div class="qt_info_heading">Possible Problems....</div>';

//no ref
echo '<div class="qt_info_heading">No Reference</div>';
qt_show_questions_with_no_ref();

//option count other than 4
echo '<div class="qt_info_heading">Not 4 Answer Options</div>';
qt_show_questions_with_not_4_answer_opitions();

//contains absolute link 'http://'
echo '<div class="qt_info_heading">Absolute Links</div>';
qt_show_questions_with_absolute_links();


echo $OUTPUT->footer();

?>