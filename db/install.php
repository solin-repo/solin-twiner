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
    $data -> eventname = '\core\event\course_created';
    $data -> action = 'notify';
    $id = $DB->insert_record('twiner_triggers', $data);

// test action for trigger
    unset($data);
    $data = new stdClass();
    $data -> creator_id = 0;
    $data -> trigger_id = $id;
    $data -> target_type = 'individual';
    $data -> target_id = 0;
    $data -> subject = 'Test subject';
    $data -> body = ' Test body';
    $DB->insert_record('twiner_notifications', $data);
    unset($data);

//Course complition - enroll trigger
    $data = new stdClass();
    $data -> eventname = '\core\event\course_completed';
    $data -> action = 'enroll';
    $id = $DB->insert_record('twiner_triggers', $data);

// test action for trigger
    unset($data);
    $data = new stdClass();
    $data -> creator_id = 1;
    $data -> trigger_id = $id;
    $DB->insert_record('twiner_enroll', $data);
    unset($data);
}

