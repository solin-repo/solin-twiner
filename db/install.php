<?php

/*
 *    Inserting default triggers.
 *    And some test data.
 */
function xmldb_local_solin_twiner_install()
{
    global $DB;

	// Course creating - notify trigger
    $data = new stdClass();
    $data->eventname = '\core\event\course_created';
    $data->action = 'notify';
    $id = $DB->insert_record('twiner_triggers', $data);
    unset($data);

	//Course completion - enroll trigger
    $data = new stdClass();
    $data->eventname = '\core\event\course_completed';
    $data->action = 'enroll';
    $id = $DB->insert_record('twiner_triggers', $data);
    unset($data);
}

