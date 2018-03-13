<style>
.question-output { font-size: 12px; }
.question-output td { padding: 2px 20px 2px 2px; }
</style>
<?php

require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/questionsInQuiz.php');
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarexportxml', 'block_question_tools'));
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$moduleId = isset($_POST['moduleId']) ? $_POST['moduleId'] : 0;

echo '<div class="qt_page_heading">Questions In Quiz</div>';

echo '<form action="" method="POST">';
echo '<b>Module ID:</b> ';
echo '<input type="number" name="moduleId" value="'.$moduleId.'" />';
echo '<input type="submit" name="random" value="Run Random Report" />';
echo '<input type="submit" name="specific" value="Run Specific Report" />';
echo '</form>';

if (isset($_POST['specific']))
{

  echo '<hr />';
  echo 'module id: '.$moduleId.'<br />';

  $quizId = $DB->get_field('course_modules', 'instance', array('id'=>$moduleId));
  echo 'quiz id: '.$quizId.'<br />';

  $quiz = $DB->get_record('quiz', array('id'=>$quizId));
  echo 'quiz: '.$quiz->name.'<br /><br />';

  $questions = explode(',', $quiz->questions);

  echo '<table class="question-output">';
  echo '<tr style="font-weight:bold;"><td>id</td><td>name</td><td>category</td><td>text</td></tr>';

  foreach ($questions as $qId) {

    $q = $DB->get_record('question', array('id'=>$qId));
    $category = $DB->get_record('question_categories', array('id'=>$q->category));
    $categoryName = $category->name;
    echo '<tr>';
    echo '<td>'.$q->id.'</td>';
    echo '<td>'.substr($q->name, 0, 50).'</td>';
    echo '<td>'.substr($categoryName, 0, 50).'</td>';
    echo '<td>'.substr($q->questionText, 0, 50).'</td>';
    echo '</tr>';

  }

  echo '</table>';

}






if (isset($_POST['random'])) {

  echo '<hr />';
  echo 'module id: '.$moduleId.'<br />';

  $quizId = $DB->get_field('course_modules', 'instance', array('id'=>$moduleId));
  echo 'quiz id: '.$quizId.'<br />';

  $quiz = $DB->get_record('quiz', array('id'=>$quizId));
  echo 'quiz: '.$quiz->name.'<br /><br />';

  $questions = $quiz->questions;

  $sql = 'SELECT c.id AS id, c.name AS cat_name, COUNT(q.name) AS question_count ';
  $sql .= 'FROM {question} q ';
  $sql .= 'JOIN {question_categories} c ON q.category = c.id ';
  $sql .= 'WHERE FIND_IN_SET(q.id, ?) GROUP BY q.name';

  echo '<table class="question-output">';
  echo '<tr style="font-weight:bold;"><td>cat id</td><td>cat name</td><td>question count</td></tr>';

  $params = array($questions);

  $results = $DB->get_recordset_sql($sql, $params);

  foreach ($results as $r) {
    echo '<tr>';
    echo '<td>'.$r->id.'</td>';
    echo '<td>'.$r->cat_name.'</td>';
    echo '<td>'.$r->question_count.'</td>';
    echo '</tr>';
  }

  echo '</table>';


}









echo $OUTPUT->footer();

?>