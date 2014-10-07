<?php

/*
 *    Inserting default triggers.
 *    And some test data.
 */
function xmldb_local_solin_twiner_install()
{
    global $DB;

	// fixme - standard events

	// Course creating - notify trigger
    $data = new stdClass();
    $data->eventname = '\core\event\course_created';
    $data->action = 'notify';
    $id = $DB->insert_record('twiner_events', $data);
    unset($data);

	//Course completion - enrol trigger
    $data = new stdClass();
    $data->eventname = '\core\event\course_completed';
    $data->action = 'enrol';
    $id = $DB->insert_record('twiner_events', $data);
    unset($data);
}

