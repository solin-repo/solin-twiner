<?php

/*
 *    Inserting default triggers.
 *    And some test data.
 */
function xmldb_local_solin_twiner_install()
{
    global $DB;
    $data = new stdClass();
    $data -> eventname = '\core\event\course_created';
    $data -> action = 'notify';
    $id = $DB->insert_record('twiner_triggers', $data);
    unset($data);
    $data = new stdClass();
    $data -> creator_id = 1;
    $data -> trigger_id = $id;
    $data -> target_type = 'individual';
    $data -> target_id = 0;
    $data -> subject = 'Test subject';
    $data -> body = ' Test body';
    $DB->insert_record('twiner_notifications', $data);
}

