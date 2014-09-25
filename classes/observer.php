<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/local/solin_twiner/locallib.php');
class local_solin_twiner_observer {

	public static function check_user_defined_handlers($event) {
        global $DB;
        $triggers = $DB->get_records('twiner_triggers', array('eventname' => $event->eventname));
		//print_object($event);
		//print_object($triggers);
        if(count($triggers))
        {
			foreach($triggers as $trigger)
			{
				switch ($trigger->action){
					case "notify":
						local_solin_twiner_notify($event,$trigger);
						break;
					case "enroll":
						local_solin_twiner_enroll($event,$trigger);
						break;
					case "group":
						local_solin_twiner_group($event,$trigger);
						break;
					case "assign":
						local_solin_twiner_assign($event,$trigger);
						break;

				}
			}
        }
    }

}