<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_login();

//page link: 1- create new trigger, 2- show triggers list, 3- define events
$page_context = optional_param('type', 2, PARAM_INT);
$PAGE->set_url('/local/solin_twiner/admin.php');
$PAGE->set_context(context_system::instance());

$twiner_url = $CFG->wwwroot . '/local/solin_twiner/admin.php';
$PAGE->requires->js('/local/solin_twiner/js/jquery-1.9.0.js');

//page output
echo $OUTPUT->header();
include('templates/menu.php');
if ($page_context == 1) include('templates/new.php');
if ($page_context == 2) include('templates/list.php');
if ($page_context == 3) include('templates/define.php');
echo $OUTPUT->footer();
die;