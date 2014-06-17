<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Processing notify action
 * @param $event    - hadnled event
 * @param $trigger  - triggers from DB
 */
function local_solin_twiner_notify($event,$trigger) {
    global $DB;
    $notifications = $DB->get_records('twiner_notifications',array('trigger_id'=>$trigger->id));
    if(count($notifications))
        foreach($notifications as $notification)
        {
            syslog(1,'local lib foreach');

            switch($notification->targettype){
                case "individual":
                    syslog(1,'local individual');
                    twiner_send($notification->target_id,$notification->subject,$notification->body);
                    break;
                case "course":
                    //notify all course users
                    break;
                case "admin":
                    //Notify admin.
                    break;
            }
        }
}

/**
 * Processing enroll action
 */
function local_solin_twiner_enroll($event,$trigger) {
    global $DB;
}

/**
 * Processing group action
 */
function local_solin_twiner_group($event,$trigger) {
    global $DB;
}

/**
 * Processing assign to cohort action
 */
function local_solin_twiner_assign($event,$trigger) {
    global $DB;
}


/**
 *
 * @param $user_id - 0 - to admin
 * @param $subject - message subject
 * @param $body - message body
 */
function twiner_send($user_id,$subject,$body)
{
    global $DB;
    $eventdata = new stdClass();
    $eventdata->component         = 'moodle'; //your component name
    $eventdata->name              = 'instantmessage'; //this is the message name from messages.php
    $eventdata->userfrom          = get_admin();
    $eventdata->userto            = ($user_id)?$DB->get_record('user',array('id'=>$user_id)):get_admin();
    $eventdata->subject           = $subject;
    $eventdata->fullmessage       = $body;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    $eventdata->notification      = 1; //this is only set to 0 for personal messages between users
    message_send($eventdata);

}