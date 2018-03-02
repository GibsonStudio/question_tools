<?php

require('../../config.php');
require('lib.php');

global $DB, $CFG, $db;

$search_string = isset($_POST['search_string']) ? trim($_POST['search_string']) : '';
gt_run_search($search_string);

?>