<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_login();

//page link: 1- create new trigger, 2- show triggers list
$page_context = optional_param('type',-1,PARAM_INT);
$PAGE->set_url('/local/solin_twiner/admin.php');
$PAGE->set_context(context_system::instance());

$twiner_url = $CFG->wwwroot.'/local/solin_twiner/admin.php';
$PAGE->requires->js('/local/solin_twiner/js/jquery-1.9.0.js');
$available_triggers = $DB->get_records('twiner_triggers');
foreach($available_triggers as &$trigger)
    eval('$trigger->name = '.$available_triggers[1]->eventname.'::get_name();');
$created_notifications = $DB->get_records('twiner_notifications');
//page output
echo $OUTPUT->header();
include('templates/menu.php');
echo $OUTPUT->footer();
die;