<?php

function xmldb_local_solin_twiner_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014092600) {

        // Define table twiner_enrol to be created.
        $table = new xmldb_table('twiner_enrol');

        // Adding fields to table twiner_enrol.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('creator_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('trigger_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table twiner_enrol.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('trigger', XMLDB_KEY_FOREIGN, array('trigger_id'), 'twiner_triggers', array('id'));
        $table->add_key('user', XMLDB_KEY_FOREIGN, array('user_id'), 'user', array('id'));
        $table->add_key('course', XMLDB_KEY_FOREIGN, array('course_id'), 'course', array('id'));

        // Conditionally launch create table for twiner_enrol.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Local savepoint reached.
        upgrade_plugin_savepoint(true, 2014092600, 'local', 'solin_twiner');
    }

    return true;
} // function xmldb_local_scheduler_upgrade

?>
