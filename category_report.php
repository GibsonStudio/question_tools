<style>
.error {
    font-weight: bold;
    color: #df0000;
</style>
<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/category_report.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarinformation', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();

echo '<div class="qt_page_heading">'.get_string('categoryreportheading', 'block_question_tools').'</div>';

$date_string = date("D M d, Y G:i");
echo "Report Date: $date_string</p>";

function get_max_questions($name)
{
    $start_pos = stripos($name, '[') + 1;
    $end_pos = stripos($name, ']');
    $length = $end_pos - $start_pos;
    $num = substr($name, $start_pos, $length) + 0;
    return $num;
}

$cats = $DB->get_records_select('question_categories', 'name LIKE "%]" ORDER BY name');

$result = '';
$error_count = 0;

foreach ($cats as $cat)
{
    
    $max_count = get_max_questions($cat->name);
    $count = $DB->count_records_select('question', 'category='.$cat->id.' AND name NOT LIKE "Random%"');
    
    if ($max_count > $count)
    {
        $result .= '<span class="error">';
        $error_count++;
    }
    
    $result .= $cat->name;
    $result .= " ($count)";
    $result .= '<br />';
    
    if ($max_count > $count)
    {
        $result .= '</span>';
    }
    
}

if ($error_count == 0)
{
    echo 'No errors.<hr />';
}
else
{
    echo $error_count.' errors....<hr />';
}
echo $result;










echo $OUTPUT->footer();

?>