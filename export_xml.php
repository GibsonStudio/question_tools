<?php
 
require_once('../../config.php');
require_once('lib.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, SITEID);
require_capability('moodle/question:editall', $context);

$PAGE->set_url('/blocks/question_tools/export_xml.php'); 
$PAGE->set_pagelayout('standard');

$settingsnode = $PAGE->settingsnav->add(get_string('navbartitle', 'block_question_tools'));
$editnode = $settingsnode->add(get_string('navbarexportxml', 'block_question_tools')); 
$editnode->make_active();

echo $OUTPUT->header();

echo qt_get_nav();
$categoryid = isset($_POST['categoryid']) ? $_POST['categoryid'] : 0;
$questions_only = isset($_POST['questions_only']) ? true : false;

echo '<div class="qt_page_heading">'.get_string('exportxmlheading', 'block_question_tools').'</div>';

echo '<form action="" method="POST">';
echo qt_get_category_select($categoryid);
echo '<br />Questions Only: <input type="checkbox" name="questions_only" ';
if ($questions_only) { echo ' checked '; }
echo '/><br />';
echo '<input type="submit" name="submit" value="Export" />';
echo '</form>';

if (isset($_POST['submit']))
{
	
	echo '<hr />';	
	
	$categories = qt_get_child_categories($categoryid); 
	
	echo '<textarea style="width:98%;height: 500px;">';
	
	if (!$questions_only)
	{
	
		echo "<QUESTIONS>\n\n";
		echo "\t<CATEGORIES>\n";
		
		foreach ($categories as $categoryid)
		{
			$category_name = trim($DB->get_field('question_categories', 'name', array('id'=>$categoryid)));
			echo "\t\t".'<CATEGORY id="'.$categoryid.'">'.qt_cdata($category_name).'</CATEGORY>'."\n";
		}
		
		
		echo "\t</CATEGORIES>\n\n";
		
		echo "\t<QUESTIONS>\n\n";
	
	}
	
	
	foreach ($categories as $categoryid)
	{
	
		$questionids = qt_get_questions_in_category($categoryid);
		
		foreach ($questionids as $questionid)
		{
			
			$question_name = $DB->get_field('question', 'name', array('id'=>$questionid));		
			$ref = explode(";", $question_name)[0];
			
			echo "\t\t".'<QUESTION ref="'.qt_cdata($ref).'" categoryid="'.$categoryid.'">'."\n";
		
			$questiontext = trim($DB->get_field('question', 'questiontext', array('id'=>$questionid)));
			
			echo "\t\t\t".'<TEXT>'.qt_cdata($questiontext).'</TEXT>'."\n";
			echo "\t\t\t"."<OPTIONS>\n";
			
			$answer_options = qt_get_answer_options($questionid);
			
			for ($i = 0; $i < count($answer_options); $i++)
			{
			
				$answer = trim($DB->get_field('question_answers', 'answer', array('id'=>$answer_options[$i])));
				$fraction = $DB->get_field('question_answers', 'fraction', array('id'=>$answer_options[$i]));
				$fraction = round($fraction, 2);
				
				echo "\t\t\t\t".'<OPTION mark="'.$fraction.'">'.qt_cdata($answer).'</OPTION>'."\n";
				
			}
			
			echo "\t\t\t</OPTIONS>\n";

			echo "\t\t</QUESTION>\n\n";
		
		}
		
	}
	
	
	
	if (!$questions_only)
	{
		echo "\t</QUESTIONS>\n\n";
		echo "</QUESTIONS>";
	}
	
	echo '</textarea>';
	
}

echo $OUTPUT->footer();

?>