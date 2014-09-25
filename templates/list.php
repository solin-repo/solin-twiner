<h4><?= get_string('list_of_triggers', 'local_solin_twiner'); ?></h4>
<?php
/*
 * list template
 */
$notify_remove = optional_param('notify_remove', 0, PARAM_INT);
if ($notify_remove)
{
    $DB->delete_records('twiner_notifications', array('id' => $notify_remove));
	echo "<p>" . get_string('trigger_removed', 'local_solin_twiner') . "</p>\n";
}

$sql = '
SELECT t1.id, t1.creator_id, t1.target_id, t1.subject, t2.eventname FROM {twiner_notifications} AS t1
    LEFT JOIN {twiner_triggers} AS t2 ON t1.trigger_id = t2.id';
$notifications = $DB->get_records_sql($sql);

if ($notifications)
{
	?>
	<h2><?=get_string('notifications','local_solin_twiner');?></h2>
		<table class="generaltable">
			<tr style="font-weight: bold;">
				<td><?=get_string('event','local_solin_twiner');?></td>
				<td><?=get_string('type','local_solin_twiner');?></td>
				<td><?=get_string('target','local_solin_twiner');?></td>
	            <td><?=get_string('subject','local_solin_twiner');?></td>
				<td>&nbsp;</td>
			</tr>
		<? foreach ($notifications as $notification):
		$user = $DB->get_record('user',array('id'=>$notification->target_id));?>
			<tr>
				<td><?=call_user_func(array($notification->eventname, 'get_name'));?></td>
				<td><?=get_string('individual','local_solin_twiner');?></td>
				<td><?=$user->firstname.' '.$user->lastname;?></td>
	            <td><?= $notification->subject; ?></td>
				<td><?=html_writer::link($twiner_url.'?type=2&notify_remove='.$notification->id,get_string('remove','local_solin_twiner'));?></td>
			</tr>
		<? endforeach;?>
		</table>
<?php
}
else
{
	echo "<p>" . get_string('no_triggers', 'local_solin_twiner') . "</p>\n";
}
?>