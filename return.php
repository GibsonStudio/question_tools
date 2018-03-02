<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/references.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarsearch', 'block_question_tools')); 
$editnode->make_active();

$returnurl = isset($_REQUEST['returnurl']) ? $_REQUEST['returnurl'] : '';

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('searchheading', 'block_question_tools').'</div>';

echo '<span style="font-weight:bold;">Question Updated</span><br />';
echo 'Question has been updated by moodle, or update was cancelled.<br/><br/>';
echo '<a href="javascript:history.go(-2)"><button>back</button></a>';

echo '<hr />Return URL:'.$returnurl;

echo $OUTPUT->footer();

?>