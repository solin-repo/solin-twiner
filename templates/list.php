<h4><?= get_string('list_of_triggers', 'local_solin_twiner'); ?></h4>
<?php
/*
 * list template
 */
$notify_remove = optional_param('notify_remove', 0, PARAM_INT);
$enrol_remove = optional_param('enrol_remove', 0, PARAM_INT);
if ($notify_remove || $enrol_remove)
{
    if ($notify_remove) $DB->delete_records('twiner_notifications', array('id' => $notify_remove));
    if ($enrol_remove) $DB->delete_records('twiner_enrol', array('id' => $enrol_remove));
	echo "<p>" . get_string('trigger_removed', 'local_solin_twiner') . "</p>\n";
}

$sql_notifications  = "SELECT tn.id, tn.creator_id, tn.target_id, tn.subject, tt.eventname ";
$sql_notifications .= "FROM {twiner_notifications} AS tn ";
$sql_notifications .= "LEFT JOIN {twiner_triggers} AS tt ON tn.trigger_id = tt.id ";
$notifications = $DB->get_records_sql($sql_notifications);

$sql_enrollments  = "SELECT te.id, te.user_id, te.course_id, tt.eventname, c.fullname ";
$sql_enrollments .= "FROM {twiner_enrol} te ";
$sql_enrollments .= "JOIN {twiner_triggers} AS tt ON te.trigger_id = tt.id ";
$sql_enrollments .= "JOIN {course} AS c ON te.course_id = c.id ";
$enrollments = $DB->get_records_sql($sql_enrollments);

if ($notifications || $enrollments)
{
	if ($notifications)
	{
		?>
		<h2><?= get_string('notifications', 'local_solin_twiner'); ?></h2>
		<table class="generaltable">
			<tr>
				<th><?= get_string('event', 'local_solin_twiner'); ?></th>
				<th><?= get_string('type', 'local_solin_twiner'); ?></th>
				<th><?= get_string('user', 'local_solin_twiner'); ?></th>
				<th><?= get_string('subject', 'local_solin_twiner'); ?></th>
				<th>&nbsp;</th>
			</tr>
		<? foreach ($notifications as $notification):
			$user = $DB->get_record('user', array('id' => $notification->target_id)); ?>
			<tr>
				<td><?= call_user_func(array($notification->eventname, 'get_name')); ?></td>
				<?php // fixme ?>
				<td><?= get_string('individual', 'local_solin_twiner'); ?></td>
				<td><?= fullname($user); ?></td>
				<td><?= $notification->subject; ?></td>
				<td><?= html_writer::link($twiner_url . '?type=2&notify_remove=' . $notification->id, get_string('remove', 'local_solin_twiner')); ?></td>
			</tr>
		<? endforeach;?>
		</table>
		<p>&nbsp;</p>
		<?php
	}
	
	if ($enrollments)
	{
		?>
		<h2><?= get_string('enrollments', 'local_solin_twiner'); ?></h2>
		<table class="generaltable">
			<tr>
				<th><?= get_string('event', 'local_solin_twiner'); ?></th>
				<th><?= get_string('user', 'local_solin_twiner'); ?></th>
				<th><?= get_string('course', 'local_solin_twiner'); ?></th>
				<th>&nbsp;</th>
			</tr>
		<? foreach ($enrollments as $enrollment):
			$user = $DB->get_record('user', array('id' => $enrollment->user_id)); ?>
			<tr>
				<td><?= call_user_func(array($enrollment->eventname, 'get_name')); ?></td>
				<td><?= fullname($user); ?></td>
				<td><?= $enrollment->fullname; ?></td>
				<td><?= html_writer::link($twiner_url . '?type=2&enrol_remove=' . $enrollment->id, get_string('remove', 'local_solin_twiner')); ?></td>
			</tr>
		<? endforeach;?>
		</table>
		<?php
	}
}
else
{
	echo "<p>" . get_string('no_triggers', 'local_solin_twiner') . "</p>\n";
}
?>