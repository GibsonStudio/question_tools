<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    moodlecore
 * @subpackage block
 * @copyright  2013 Jon Williams
 * @author     2013 Jon Williams
 * @version    1.0
 */

class block_question_tools extends block_base {

    function init() {
        $this->title = get_string('pluginname','block_question_tools');
    }

    function get_content() {

		$context = get_context_instance(CONTEXT_SYSTEM);

		if ( !isloggedin() || !has_capability('moodle/question:editall', $context) ) {

			$this->content = null;

		} else {

			global $PAGE, $CFG;

			if ($this->content !== NULL) {
				return $this->content;
			}

			$this->content = new stdClass;

			global $COURSE;

			//Search Questions Link
			$url = new moodle_url('/blocks/question_tools/search_questions.php'); //, array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
			$this->content->text = '<a href="'.$url.'">'.get_string('searchlink', 'block_question_tools').'</a><br />';

			//View by category
			$url = new moodle_url('/blocks/question_tools/view_by_category.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('viewbycategorylink', 'block_question_tools').'</a><br />';

			//Add / Edit References
			$url = new moodle_url('/blocks/question_tools/references.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('referenceslink', 'block_question_tools').'</a><br />';

			//Show Where used
			$url = new moodle_url('/blocks/question_tools/show_where_used.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('showwhereusedlink', 'block_question_tools').'</a><br />';

      //category report
      $url = new moodle_url('/blocks/question_tools/category_report.php');
      $this->content->text .= '<a href="'.$url.'">'.get_string('categoryreportlink', 'block_question_tools').'</a><br />';

			//Export XML
			$url = new moodle_url('/blocks/question_tools/export_xml.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('exportxmllink', 'block_question_tools').'</a><br />';

      //Export Pelesys CSV
			$url = new moodle_url('/blocks/question_tools/export_pelesys_csv.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('exportcsvlink', 'block_question_tools').'</a><br />';

      //Questions In Quiz
			$url = new moodle_url('/blocks/question_tools/questionsInQuiz.php');
			$this->content->text .= '<a href="'.$url.'">Questions In Quiz</a><br />';

			//Images
			$url = new moodle_url('/blocks/question_tools/images.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('imageslink', 'block_question_tools').'</a><br />';

			//Information
			$url = new moodle_url('/blocks/question_tools/information.php');
			$this->content->text .= '<a href="'.$url.'">'.get_string('informationlink', 'block_question_tools').'</a><br />';


		}

		return $this->content;

    }

}
?>
