<style>
#question-output { font-size: 12px; }
#question-output td { padding: 2px 20px 2px 2px; }
</style>
<script>
function saveTextAreaAsCSV (elID) {
  var txt = $('#' + elID).val();
  var el = document.createElement('a');
  el.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(txt));
  el.setAttribute('download', elID + '.csv');
  el.style.display = 'none';
  document.body.appendChild(el);
  el.click();
  document.body.removeChild(el);
}
</script>

<?php

require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/export_pelesys_csv.php');
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarexportxml', 'block_question_tools'));
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$categoryId = isset($_POST['categoryid']) ? $_POST['categoryid'] : 0;

echo '<div class="qt_page_heading">'.get_string('exportcsvheading', 'block_question_tools').'</div>';

echo '<form action="" method="POST">';
echo qt_get_category_select($categoryId);
echo '<input type="submit" name="submit" value="Export" />';
echo '</form>';

if (isset($_POST['submit']))
{

  echo '<button onclick="javascript:saveTextAreaAsCSV(\'csvQuestions\');">Save Questions CSV</button>';

	echo '<hr />';

	$categories = qt_get_child_categories($categoryId);

	echo '<textarea id="csvQuestions" style="width:98%;height: 200px;font-size:10px;">';

  $choiceCount = 10;
  $imagesRequired = [];

  // add row 1 (field titles)
  echo 'SID,Topic Name,SubTopicName,Short Name,Reference,Question Title,Correct Answer,';

  // add field titles for Choices
  for ($i = 1; $i <= $choiceCount; $i++) {
    echo 'Choice '.$i;
    if ($i < $choiceCount) { echo ','; }
  }

  // add feedback field titles?
  //echo ',Feedback(Correct),Feedback(incorrect)';

  // start new line - ready for question data
  echo "\n";



	foreach ($categories as $categoryId)
	{

    $categoryName = trim($DB->get_field('question_categories', 'name', array('id'=>$categoryId)));
    $parentId = trim($DB->get_field('question_categories', 'parent', array('id'=>$categoryId)));
    $parentName = trim($DB->get_field('question_categories', 'name', array('id'=>$parentId)));

    // limit string lengths
    $categoryName = substr($categoryName, 0, 35);
    $parentName = substr($parentName,0, 35);


		$questionIds = qt_get_questions_in_category($categoryId);

		foreach ($questionIds as $questionId)
		{

      $reference = qt_getQuestionRef($questionId);
      $questionText = trim($DB->get_field('question', 'questiontext', array('id'=>$questionId)));
      $shortName = substr($questionText, 0, 50);

      // contains image?
      if (strpos($questionText, 'media.caeoxfordinteractive') || strpos($questionText, 'PLUGINFILE')) {
        $imageName = qtGetImageNameFromText($questionText);
        $shortName = '***IMG***('.$imageName.')';
        array_push($imagesRequired, [$questionId, $reference, $imageName]);
      }

      echo fixData($questionId).',';
      echo fixData($parentName).',';
      echo fixData($categoryName).',';
      echo fixData($shortName).',';
      echo fixData($reference).',';
      echo fixData($questionText).',';

      // get answer options and correct answer

      $answerOptions = qt_get_answer_options($questionId);
      $correctAnswer = 0;
      $answers = array();

      for ($i = 0; $i < count($answerOptions); $i++) {

        $answerText = trim($DB->get_field('question_answers', 'answer', array('id'=>$answerOptions[$i])));
        array_push($answers, $answerText);
				$fraction = $DB->get_field('question_answers', 'fraction', array('id'=>$answerOptions[$i]));
        if ($fraction > 0) { $correctAnswer = $i + 1; }

      }

      echo fixData($correctAnswer).',';

      for ($i = 0; $i < count($answers); $i++) {
        echo fixData($answers[$i]);
        if ($i < count($answers) - 1) { echo ','; }
      }

      // add extra commas?
      for ($i = count($answers); $i < $choiceCount; $i++) {
        echo ',';
      }

      echo "\n";

		}

	}


  // close text area
	echo '</textarea>';




  // images required?
  if (count($imagesRequired)) {

      echo '<div style="font-weight:bold;font-size:20px;color:#FFFFFF;background-color:#F9423A;padding:4px;width:98%;">'.count($imagesRequired).' Images Required</div>';
      echo '<button onclick="javascript:saveTextAreaAsCSV(\'csvImages\');">Save Image CSV</button>';
      echo '<textarea id="csvImages" style="width:98%;height: 200px;font-size:10px;">';
      echo "questionId, questionRef, imageFilename\n";

      foreach ($imagesRequired as $image) {
        echo $image[0].',';
        echo $image[1].',';
        echo $image[2]."\n";
      }

      echo '</textarea>';

  }




}

echo $OUTPUT->footer();

?>
