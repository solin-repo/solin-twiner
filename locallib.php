<?php

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
			$current_instance = false;
			if (!is_enrolled(context_course::instance($enrollment->course_id), $enrollment->user_id))
			{
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
				
				if ($current_instance !== false) $enrollment_plugin->enrol_user($current_instance, $enrollment->user_id);
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
}

/**
 * Processing assign to cohort action
 * @param $event    - handled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_assign($event, $trigger) {
    global $DB;
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
