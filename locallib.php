<?php
// Fixme - add readme


defined('MOODLE_INTERNAL') || die();

/**
 * Processing notify action
 * @param $event    - handled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_notify($event, $trigger) {
    global $DB;
    $notifications = $DB->get_records('twiner_notifications', array('trigger_id' => $trigger->id));
    if (count($notifications))
	{
        foreach ($notifications as $notification)
        {
            switch ($notification->targettype){
                case "individual":
					// Notify individual user
                    twiner_send($notification->target_id, $notification->subject, $notification->body);
                    break;
                case "course":
                    // Notify all course users
					$users_in_course = get_enrolled_users(context_course::instance($notification->target_id));
					if (count($users_in_course) > 0)
					{
						foreach ($users_in_course as $tmp_user)
						{
					        twiner_send($tmp_user->id, $notification->subject, $notification->body);
						}
					}
                    break;
            }
        }
	}
}

/**
 * Processing enrol action
 * @param $event    - handled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_enrol($event, $trigger) {
    global $DB, $CFG;
    $enrollments = $DB->get_records('twiner_enrol', array('trigger_id' => $trigger->id));
    if (count($enrollments))
	{
		$enrollment_plugin = enrol_get_plugin('manual');
		foreach ($enrollments as $enrollment)
		{
			// Check if it's about a new user
			$tmp_user_id = $enrollment->user_id;
			if ($enrollment->user_id == -1) $tmp_user_id = $event->objectid;
			
			// Check if the user is not already enrolled
			$current_instance = false;
			if (!is_enrolled(context_course::instance($enrollment->course_id), $tmp_user_id))
			{
				$role_id = $DB->get_field_sql("SELECT id FROM {role} WHERE shortname = 'student';");
				if ($instances = enrol_get_instances($enrollment->course_id, false)) 
				{
					foreach ($instances as $instance) 
					{
						if ($instance->enrol === 'manual') 
						{
							$current_instance = $instance;
							break;
						}
					}
				}
				
				if ($current_instance !== false) $enrollment_plugin->enrol_user($current_instance, $tmp_user_id, $role_id, time());
			}
		}
	}
}

/**
 * Processing group action
 * @param $event    - handled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_group($event, $trigger) {
    global $DB;

    $groups = $DB->get_records('twiner_groups', array('trigger_id' => $trigger->id));
    if (count($groups))
	{
        foreach ($groups as $group)
        {
			// Check if it's about a new user
			$tmp_user_id = $group->user_id;
			if ($group->user_id == -1) $tmp_user_id = $event->objectid;

			// Check if the user is not in the group already
			if (!count($DB->get_records('groups_members', array('groupid' => $group->group_id, 'userid' => $tmp_user_id))))
			{
				$record = new stdClass();
				$record->groupid = $group->group_id;
				$record->userid = $tmp_user_id;
				$record->timeadded = time();
				$DB->insert_record('groups_members', $record);
			}
		}
	}
}

/**
 * Processing assign to cohort action
 * @param $event    - handled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_assign_cohort($event, $trigger) {
    global $DB;

	$cohorts = $DB->get_records('twiner_cohorts', array('trigger_id' => $trigger->id));
    if (count($cohorts))
	{
        foreach ($cohorts as $cohort)
        {
			// Check if it's about a new user
			$tmp_user_id = $cohort->user_id;
			if ($cohort->user_id == -1) $tmp_user_id = $event->objectid;

			// Check if the user is not in the group already
			if (!count($DB->get_records('cohort_members', array('cohortid' => $cohort->cohort_id, 'userid' => $tmp_user_id))))
			{
				$record = new stdClass();
				$record->cohortid = $cohort->cohort_id;
				$record->userid = $tmp_user_id;
				$record->timeadded = time();
				$DB->insert_record('cohort_members', $record);
			}
		}
	}
}


/**
 *
 * @param $user_id - 0 - to admin
 * @param $subject - message subject
 * @param $body - message body
 */
function twiner_send($user_id, $subject, $body) 
{
    global $DB;
   
	$eventdata = new stdClass();
    $eventdata->component         = 'moodle'; //your component name
    $eventdata->name              = 'instantmessage'; //this is the message name from messages.php
    $eventdata->userfrom          = get_admin();
    $eventdata->userto            = (($user_id)?$DB->get_record('user', array('id' => $user_id)):get_admin());
    $eventdata->subject           = $subject;
    $eventdata->fullmessage       = $body;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    $eventdata->notification      = 1; //this is only set to 0 for personal messages between users

	message_send($eventdata);
}
