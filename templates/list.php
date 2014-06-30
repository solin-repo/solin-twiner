<?
/*
 * list template
 */
$sql = '
SELECT t1.id,t1.creator_id,t1.targettype,t1.target_id,t2.eventname FROM {twiner_notifications} AS t1
    LEFT JOIN {twiner_triggers} AS t2 ON t1.trigger_id=t2.id';
$notifications = $DB->get_records_sql($sql);
if($notifications):?>
<h2><?=get_string('notifications','local_solin_twiner');?></h2>
    <table class="generaltable">
        <tr>
            <td><?=get_string('event','local_solin_twiner');?></td>
            <td><?=get_string('type','local_solin_twiner');?></td>
            <td><?=get_string('target','local_solin_twiner');?></td>
        </tr>
    <? foreach ($notifications as $notification):
    $user = $DB->get_record('user',array('id'=>$notification->target_id));?>
        <tr>
            <td><?=call_user_func(array($notification->eventname, 'get_name'));?></td>
            <td><?=get_string('individual','local_solin_twiner');?></td>
            <td><?=$user->firstname.' '.$user->lastname;?></td>

        </tr>
    <? endforeach;?>
    </table>
<? endif;?>