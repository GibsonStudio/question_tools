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
$editnode = $settingsnode->add(get_string('navbarimages', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('imagesheading', 'block_question_tools').'</div>';

//select
$select_sql = 'SELECT DISTINCT {question}.id FROM {question} ';

//count
$count_sql = 'SELECT COUNT(DISTINCT {question}.id) FROM {question} ';

//clause
$sql .= 'JOIN {question_answers} ON {question_answers}.question = {question}.id ';
$sql .= 'WHERE {question}.questiontext LIKE "%<img src%" ';
$sql .= 'OR {question}.questiontext LIKE "%PLUGINFILE%" ';
$sql .= 'OR {question_answers}.answer LIKE "%<img src%" ';
$sql .= 'OR {question_answers}.answer LIKE "%PLUGINFILE%"';

$questions = $DB->get_recordset_sql($select_sql.$sql);
$count = $DB->count_records_sql($count_sql.$sql);

echo '<b>'.$count.' question(s) have images....</b><hr />';

foreach ($questions as $question)
{
	echo qt_get_question($question->id);
}

echo $OUTPUT->footer();

?>