<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/local/solin_twiner/locallib.php');
class local_solin_twiner_observer {

	public static function check_user_defined_handlers($event) {
        global $DB;
        $twiner_events = $DB->get_records('twiner_events', array('eventname' => $event->eventname));
		//print_object($event);
		//print_object($twiner_events);
        if(count($twiner_events))
        {
			foreach($twiner_events as $twiner_event)
			{
				switch ($twiner_event->action){
					case "notify":
						local_solin_twiner_notify($event, $twiner_event);
						break;
					case "enrol":
						local_solin_twiner_enrol($event, $twiner_event);
						break;
					case "group":
						local_solin_twiner_group($event, $twiner_event);
						break;
					case "cohort":
						local_solin_twiner_assign_cohort($event, $twiner_event);
						break;

				}
			}
        }
    }

}
?>