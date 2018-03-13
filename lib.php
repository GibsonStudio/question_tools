<?php

function fixData ($data)
{

  // remove any line breaks
  $data = str_replace("\r", "", $data);
  $data = str_replace("\n", "<br />", $data);

  // double quotes
  if (strpos($data, '"') !== false) {
    $data = str_replace('"', '""', $data);
    return '"'.$data.'"';
  }

  // commas?
  if (strpos($data, ',') !== false) { return '"'.$data.'"';   }

  // single quotes?
  if (strpos($data, "'") !== false) { return '"'.$data.'"';   }

  return $data;

}



function qt_getQuestionRef ($questionId)
{

  global $DB;

  $questionName = trim($DB->get_field('question', 'name', array('id'=>$questionId)));
  $index = strpos($questionName, ';');

  if ($index) {
    return substr($questionName, 0, $index);
  }

  return 'NOREF-'.$questionId;

}


function qt_get_nav ()
{

	$n = '<div class="qt_navigation">';
	$n .= '<a href="search_questions.php"><span class="qt_nav_link">Search</span></a>';
	$n .= '<a href="view_by_category.php"><span class="qt_nav_link">View by Category</span></a>';
	$n .= '<a href="references.php"><span class="qt_nav_link">Add / Edit References</span></a>';
	$n .= '<a href="show_where_used.php"><span class="qt_nav_link">Show Where Used</span></a>';
  $n .= '<a href="category_report.php"><span class="qt_nav_link">Category Report</span></a>';
	$n .= '<a href="export_xml.php"><span class="qt_nav_link">Export XML</span></a>';
  $n .= '<a href="questionsInQuiz.php"><span class="qt_nav_link">Questions In Quiz</span></a>';
	$n .= '<a href="images.php"><span class="qt_nav_link">Images</span></a>';
	$n .= '<a href="information.php"><span class="qt_nav_link">Information</span></a>';
	$n .= '<a href="custom_query.php"><span class="qt_nav_link">Custom Query</span></a>';
	$n .= '</div>';

	return $n;

}



/*
function qt_connect_moodle ()
{
	global $CFG;
	$db = new PDO('mysql:host='.$CFG->dbhost.';dbname='.$CFG->dbname, $CFG->dbuser, $CFG->dbpass);
	return $db;
}
*/



function gt_run_search ($search_string)
{

	global $DB;

	if (!empty($search_string))
	{

		$like = '%'.$search_string.'%';
		$params = array($like, $like, $like);

		//select sql
		$select_sql = 'SELECT DISTINCT {question}.id FROM {question} ';

		//count sql
		$count_sql = 'SELECT COUNT(DISTINCT {question}.id) FROM {question} ';

		//clause sql
		$clause_sql = 'JOIN {question_answers} ON {question_answers}.question = {question}.id ';
		$clause_sql .= ' WHERE {question}.questiontext LIKE ?
					OR {question_answers}.answer LIKE ?
			        OR {question}.name LIKE ?';

		$record_count = $DB->count_records_sql($count_sql.$clause_sql, $params);
		$records = $DB->get_recordset_sql($select_sql.$clause_sql, $params);

		$plural = 's';

		if ($record_count == 1) {
			$plural = '';
		}

		echo '<div class="qt_questions_found">'.$record_count.' question'.$plural.' found....</div><hr />';


		foreach ($records as $r)
		{
			echo qt_get_question($r->id, array('search_string'=>$search_string));
		}



	} else {

		echo '<div class="qt_error">No data sent.</div>';

	}



}











function qt_get_category_select ($selected = 0)
{

	$s = '<select name="categoryid">';

	$s .= qt_get_category_options(0,'',$selected);

	$s .= '</select>';

	return $s;

}






function qt_get_category_options ($parent = 0, $indent = '', $selected = 0)
{

	global $DB;
	$o = '';
	$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;';

	$sql = 'SELECT id, name FROM {question_categories} WHERE parent=? AND name NOT LIKE "Default for%" ORDER BY sortorder, name';
	$params = array($parent);
	$children = $DB->get_recordset_sql($sql, $params);

	foreach ($children as $child)
	{

		//get question count
		$question_count = qt_get_question_count($child->id);

		$o .= '<option value="'.$child->id.'"';
		if ($child->id == $selected) { $o .= ' selected '; }
		$o .= '>'.$indent.$child->name.' ('.$question_count.')</option>';

		//add children
		$o .= qt_get_category_options($child->id, $indent.$spacer, $selected);

	}

	return $o;

}








function qt_get_question_count ($categoryid = 0)
{
	global $DB;
	$sql = 'SELECT COUNT(id) FROM {question} WHERE category=? AND name NOT LIKE "Random%"';
	$params = array($categoryid);
	return $DB->count_records_sql($sql, $params);
}








function qt_get_questions_in_category ($categoryid = 0)
{

	global $DB;
	$questionids = array();

	$sql = 'SELECT id FROM {question} WHERE category=? AND name NOT LIKE "%Random (%" AND NOT questiontext="1"';
	$params = array($categoryid);
	$questions = $DB->get_recordset_sql($sql, $params);

	foreach ($questions as $question)
	{
		array_push($questionids, $question->id);
	}

	return $questionids;
}









function qt_get_question_specifically_used ($questionid = 0)
{

	//returns an array of quiz ids of quizes that use the question
	global $DB;
	$quizids = array();


	$sql = 'SELECT DISTINCT quiz FROM {quiz_question_instances} WHERE question=?';
	$params = array($questionid);
	$quizzes = $DB->get_recordset_sql($sql, $params);

	foreach ($quizzes as $quiz)
	{
		array_push($quizids, $quiz->quiz);
	}

	return $quizids;

}









function qt_get_question_randomly_used ($questionid = 0)
{

	//returns an array of quiz ids of quizes that could use the question

	global $DB;
	$quizids = array();

	$categoryid = $DB->get_field('question', 'category', array('id'=>$questionid));

	$sql = 'SELECT DISTINCT quiz FROM {quiz_question_instances} ';
	$sql .= 'WHERE question IN (SELECT id FROM {question} WHERE category=?)';
	$params = array($categoryid);
	$quizzes = $DB->get_recordset_sql($sql, $params);

	foreach ($quizzes as $quiz)
	{
		array_push($quizids, $quiz->quiz);
	}

	return $quizids;

}








function qt_get_used_in_table ( $args = array() )
{

	global $DB;

	$title = isset($args['title']) ? $args['title'] : '';
	$quizids = isset($args['id_array']) ? $args['id_array'] : array();

	$t = '';

	$t .= '<table class="qt_table">';
	$t .= '<tr><td colspan="5" class="qt_table_title">'.$title.'</td></tr>';
	$t .= '<tr><td class="qt_table_heading">Course ID</td><td class="qt_table_heading">Course Name</td>';
	$t .= '<td class="qt_table_heading">Quiz ID</td><td class="qt_table_heading">Quiz Name</td>';
	$t .='<td class="qt_table_heading"></td></tr>';

	$t .= '<tr class="">';

	foreach ($quizids as $quizid)
	{

		$courseid = $DB->get_field('quiz', 'course', array('id'=>$quizid));
		$coursename = $DB->get_field('course', 'fullname', array('id'=>$courseid));
		$quizname = $DB->get_field('quiz', 'name', array('id'=>$quizid));
		$cm = get_coursemodule_from_instance('quiz', $quizid, $courseid, false, MUST_EXIST);
		$moduleid = $cm->id;

		$t .= '<tr><td class="qt_table_data">'.$courseid.'</td><td class="qt_table_data">'.$coursename.'</td>';
		$t .= '<td class="qt_table_data">'.$quizid.'</td><td class="qt_table_data">'.$quizname.'</td>';
		$t .= '<td><a href="'.$CFG->wwwroot.'/mod/quiz/view.php?id='.$moduleid.'">';
		$t .= '<button>View Quiz</button></a></td></tr>';

	}

	$t .= '</table>';

	return $t;

}








function qt_get_child_categories ($categoryid)
{

	global $DB;
	$cats = [$categoryid];

	$sql = 'SELECT id FROM {question_categories} WHERE parent=?';
	$params = array($categoryid);
	$categories = $DB->get_recordset_sql($sql, $params);

	if ($categories->valid())
	{

		$cats = [];

		foreach ($categories as $category)
		{
			array_push($cats, $category->id);
		}

	}

	return $cats;

}








function qt_cdata ($string = '')
{

	if (
		(strpos($string, '<') !== false) ||
		(strpos($string, '>') !== false) ||
		(strpos($string, 'src="') !== false) ||
		(strpos($string, '@@PLUGINFILE@@') !== false)
		)
	{
		return '<![CDATA['.$string.']]>';
	}

	return $string;

}






function qt_get_question ($questionid = 1, $args = array())
{

	global $DB, $CFG;

	$search_string = isset($args['search_string']) ? $args['search_string'] : '';
	$q = '';

	$questiontext = $DB->get_field('question', 'questiontext', array('id'=>$questionid));
	$questiontext = trim(qt_hilite($questiontext, $search_string));

	//get answer options
	$option_labels = array('a)','b)','c)','d)','e)','f)','g)');
	$answer_options = qt_get_answer_options($questionid); //TODO

	$question_name = $DB->get_field('question', 'name', array('id'=>$questionid));
	$categoryid = $DB->get_field('question', 'category', array('id'=>$questionid)); //TODO default 0
	$category = $DB->get_field('question_categories', 'name', array('id'=>$categoryid)); //TODO default ''

	$q .= '<table class="qt_question">';
	$q .= '<tr><td class="qt_question_heading"><b>Question '.$questionid.':</b> '.$category.' > '.$question_name.'</td></tr>';
	$q .= '<tr><td class="qt_question_text">';
	$q .= $questiontext.'<br /><br />';

	for ($i = 0; $i < count($answer_options); $i++)
	{
		$answer = $DB->get_field('question_answers', 'answer', array('id'=>$answer_options[$i]));
		$fraction = $DB->get_field('question_answers', 'fraction', array('id'=>$answer_options[$i]));
		$answer = trim(qt_hilite($answer, $search_string));
		$q .= $option_labels[$i].' '.$answer;
		if ($fraction > 0) { $q .= '<span class="qt_tick">&#10004;</span>'; }
		$q .= '<br />';
	}

	$q .= '</td></tr>';
	$q .= '<tr><td class="qt_question_buttons">';

	//question buttons
	//$returnurl = htmlspecialchars('/blocks/question_tools/'.basename($_SERVER['PHP_SELF'])); //return.php');
	$returnurl = htmlspecialchars('/blocks/question_tools/return.php?returnurl='.basename($_SERVER['PHP_SELF']));
	$editlink.= $CFG->wwwroot.'/question/question.php?id='.$questionid.'&courseid=1&returnurl='.$returnurl;
	$q .= '<a href="'.$editlink.'"><button>Edit</button></a>';

	$q .= '<a href="show_where_used.php?questionid='.$questionid.'&submit=1"><button>Show Where Used</button></a>';

	$previeiwlink = $CFG->wwwroot.'/question/preview.php?id='.$questionid; //.'&courseid=0';
	$q .= '<a href="'.$previeiwlink.'" target="_new"><button>Preview</button></a>';

	$q .= '</td></tr>';
	$q .= '</table>';

	return $q;

}









function qt_get_answer_options ($questionid = 0)
{

	global $DB;
	$answer_options = array();

	$sql = 'SELECT id FROM {question_answers} WHERE question=?';
	$params = array($questionid);
	$answers = $DB->get_recordset_sql($sql, $params);

	foreach ($answers as $answer)
	{
		array_push($answer_options, $answer->id);
	}

	return $answer_options;

}










function qt_hilite ($text, $search_string)
{

	if ( strpos($text, '="') > 0 ) {

		return $text;

	}
	else
	{
		return preg_replace('/'.$search_string.'/i', '<span class="qt_hilite">'.$search_string.'</span>', $text);
	}

}







function qt_get_category_count ($args)
{

	global $DB;
	$with_questions = isset($args['with_questions']) ? $args['with_questions'] : false;
	$count = 0;

	if ($with_questions) {
		$sql = 'SELECT COUNT(DISTINCT category) FROM {question}';
	}
	else {
		$sql = 'SELECT COUNT(*) FROM {question_categories} WHERE info NOT LIKE "%The default category for%"';
	}

	$count = $DB->count_records_sql($sql);

	return $count;

}








function qt_get_total_question_count ()
{

	global $DB;
	$sql = 'SELECT COUNT(id) FROM {question}';
	$count = $DB->count_records_sql($sql);

	return $count;

}







function qt_get_question_count_with_images ()
{

	global $DB;
	$sql = 'SELECT COUNT(DISTINCT {question}.id) FROM {question} ';
	$sql .= 'JOIN {question_answers} ON {question_answers}.question = {question}.id ';
	$sql .= 'WHERE {question}.questiontext LIKE "%<img src%" ';
	$sql .= 'OR {question}.questiontext LIKE "%PLUGINFILE%" ';
	$sql .= 'OR {question_answers}.answer LIKE "%<img src%" ';
	$sql .= 'OR {question_answers}.answer LIKE "%PLUGINFILE%" ';

	$count = $DB->count_records_sql($sql);

	return $count;

}








function qt_show_questions_with_no_ref ()
{

	global $DB, $CFG;
	$sql = 'SELECT * FROM {question} WHERE name NOT LIKE "%;%" AND name NOT LIKE "Random (%"';
	$questions = $DB->get_recordset_sql($sql);

	if (!$questions->valid())
	{

		echo 'All questions have a reference';

	} else {

		$count = $DB->count_records_sql('SELECT COUNT(*) FROM {question} WHERE name NOT LIKE "%;%" AND name NOT LIKE "Random (%"');
		echo '<b>'.$count.' questions have no reference....</b><hr />';

		foreach ($questions as $question)
		{

			echo '<table class="qt_question_short"><tr>';
			echo '<td>'.$question->id.'</td>';
			echo '<td>'.$question->name.'</td>';
			echo '<td>';

			$previeiwlink = 'http://localhost/moodle251/question/preview.php?id='.$question->id;
			echo '<a href="'.$previeiwlink.'" target="_new"><button>Preview</button></a>';

			$returnurl = htmlspecialchars('/blocks/question_tools/'.basename($_SERVER['PHP_SELF'])); //return.php');
			$editlink = $CFG->wwwroot.'/question/question.php?id='.$question->id.'&courseid=1&returnurl='.$returnurl;
			echo '<a href="'.$editlink.'"><button>Edit</button></a>';

			echo '</td>';
			echo '</tr></table>';

		}

	}

}







function qt_show_questions_with_absolute_links ()
{

	global $DB;

	//select sql
	$select_sql = 'SELECT DISTINCT {question}.id FROM {question} ';

	//count sql
	$count_sql = 'SELECT COUNT(DISTINCT {question}.id) FROM {question} ';

	//clause sql
	$clause_sql = 'JOIN {question_answers} ON {question_answers}.question = {question}.id ';
	$clause_sql .= 'WHERE {question}.questiontext LIKE "%http://%" ';
	$clause_sql .= 'OR {question}.questiontext LIKE "%www.%" ';
	$clause_sql .= 'OR {question_answers}.answer LIKE "%http://%" ';
	$clause_sql .= 'OR {question_answers}.answer LIKE "%www.%"';

	$questions = $DB->get_recordset_sql($select_sql.$clause_sql);
	$count = $DB->count_records_sql($count_sql.$clause_sql);

	echo '<b>'.$count.' questions have an absolute link....</b><hr />';

	foreach ($questions as $question)
	{
		echo qt_get_question($question->id, array('search_string'=>'http:'));
	}

}








function qt_show_questions_with_not_4_answer_opitions ()
{

	global $DB;

	$select_sql = 'SELECT *, COUNT(question) AS aCount FROM {question_answers} GROUP BY question HAVING  COUNT(question) <> 4';
	$questions = $DB->get_recordset_sql($select_sql);

	$count = 0;

	foreach ($questions as $question) {
		$count++;
	}

	echo '<b>'.$count.' questions do not have 4 answer options....</b><hr />';

	$questions = $DB->get_recordset_sql($select_sql);

	foreach ($questions as $question)
	{
		echo qt_get_question($question->question);
	}

}








function gt_get_url()
{

	$pageURL = 'http';

	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	return $pageURL;

}







function qt_get_question_as_html ($id = 0)
{
	return $id.'<hr />';
}









?>
