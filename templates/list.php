<?php
//------------------------------------------------------------------------------
// List template
//------------------------------------------------------------------------------
echo "<h4>" . get_string('list_of_triggers', 'local_solin_twiner') . "</h4>\n";


//--------------------
// Remove triggers
//--------------------
$notify_remove = optional_param('notify_remove', 0, PARAM_INT);
$enrol_remove = optional_param('enrol_remove', 0, PARAM_INT);
$group_remove = optional_param('group_remove', 0, PARAM_INT);
$cohort_remove = optional_param('cohort_remove', 0, PARAM_INT);
if ($notify_remove || $enrol_remove || $group_remove || $cohort_remove)
{
    if ($notify_remove) $DB->delete_records('twiner_notifications', array('id' => $notify_remove));
    if ($enrol_remove) $DB->delete_records('twiner_enrol', array('id' => $enrol_remove));
    if ($group_remove) $DB->delete_records('twiner_groups', array('id' => $group_remove));
    if ($cohort_remove) $DB->delete_records('twiner_cohorts', array('id' => $cohort_remove));
	echo "<p>" . get_string('trigger_removed', 'local_solin_twiner') . "</p>\n";
}


//--------------------
// Info triggers
//--------------------
$sql_notifications  = "SELECT tn.id, tn.creator_id, tn.target_id, tn.subject, tt.eventname ";
$sql_notifications .= "FROM {twiner_notifications} AS tn ";
$sql_notifications .= "LEFT JOIN {twiner_triggers} AS tt ON tn.trigger_id = tt.id ";
$notifications = $DB->get_records_sql($sql_notifications);

$sql_enrollments  = "SELECT te.id, te.user_id, te.course_id, tt.eventname, c.fullname ";
$sql_enrollments .= "FROM {twiner_enrol} te ";
$sql_enrollments .= "JOIN {twiner_triggers} AS tt ON te.trigger_id = tt.id ";
$sql_enrollments .= "JOIN {course} AS c ON te.course_id = c.id ";
$enrollments = $DB->get_records_sql($sql_enrollments);

$sql_groups  = "SELECT tg.id, tg.user_id, tg.course_id, tg.group_id, tt.eventname, c.fullname, g.name ";
$sql_groups .= "FROM {twiner_groups} tg ";
$sql_groups .= "JOIN {twiner_triggers} AS tt ON tg.trigger_id = tt.id ";
$sql_groups .= "JOIN {course} AS c ON tg.course_id = c.id ";
$sql_groups .= "JOIN {groups} AS g ON tg.group_id = g.id ";
$groups = $DB->get_records_sql($sql_groups);

$sql_cohorts  = "SELECT tc.id, tc.user_id, tc.cohort_id, tt.eventname, ch.name ";
$sql_cohorts .= "FROM {twiner_cohorts} tc ";
$sql_cohorts .= "JOIN {twiner_triggers} AS tt ON tc.trigger_id = tt.id ";
$sql_cohorts .= "JOIN {cohort} AS ch ON tc.cohort_id = ch.id ";
$cohorts = $DB->get_records_sql($sql_cohorts);

//--------------------
// Display triggers
//--------------------
if ($notifications || $enrollments || $groups || $cohorts)
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
			if ($enrollment->user_id > 0) $user = $DB->get_record('user', array('id' => $enrollment->user_id)); ?>
			<tr>
				<td><?= call_user_func(array($enrollment->eventname, 'get_name')); ?></td>
				<td><?= (($enrollment->user_id > 0)?fullname($user):get_string('newly_created', 'local_solin_twiner')); ?></td>
				<td><?= $enrollment->fullname; ?></td>
				<td><?= html_writer::link($twiner_url . '?type=2&enrol_remove=' . $enrollment->id, get_string('remove', 'local_solin_twiner')); ?></td>
			</tr>
		<? endforeach;?>
		</table>
		<p>&nbsp;</p>
		<?php
	}

	if ($groups)
	{
		?>
		<h2><?= get_string('groups', 'local_solin_twiner'); ?></h2>
		<table class="generaltable">
			<tr>
				<th><?= get_string('event', 'local_solin_twiner'); ?></th>
				<th><?= get_string('user', 'local_solin_twiner'); ?></th>
				<th><?= get_string('course', 'local_solin_twiner'); ?></th>
				<th><?= get_string('group_header', 'local_solin_twiner'); ?></th>
				<th>&nbsp;</th>
			</tr>
		<? foreach ($groups as $group):
			if ($group->user_id > 0) $user = $DB->get_record('user', array('id' => $group->user_id)); ?>
			<tr>
				<td><?= call_user_func(array($group->eventname, 'get_name')); ?></td>
				<td><?= (($group->user_id > 0)?fullname($user):get_string('newly_created', 'local_solin_twiner')); ?></td>
				<td><?= $group->fullname; ?></td>
				<td><?= $group->name; ?></td>
				<td><?= html_writer::link($twiner_url . '?type=2&group_remove=' . $group->id, get_string('remove', 'local_solin_twiner')); ?></td>
			</tr>
		<? endforeach;?>
		</table>
		<p>&nbsp;</p>
		<?php
	}

	if ($cohorts)
	{
		?>
		<h2><?= get_string('cohorts', 'local_solin_twiner'); ?></h2>
		<table class="generaltable">
			<tr>
				<th><?= get_string('event', 'local_solin_twiner'); ?></th>
				<th><?= get_string('user', 'local_solin_twiner'); ?></th>
				<th><?= get_string('cohort_header', 'local_solin_twiner'); ?></th>
				<th>&nbsp;</th>
			</tr>
		<? foreach ($cohorts as $cohort):
			if ($cohort->user_id > 0) $user = $DB->get_record('user', array('id' => $cohort->user_id)); ?>
			<tr>
				<td><?= call_user_func(array($cohort->eventname, 'get_name')); ?></td>
				<td><?= (($cohort->user_id > 0)?fullname($user):get_string('newly_created', 'local_solin_twiner')); ?></td>
				<td><?= $cohort->name; ?></td>
				<td><?= html_writer::link($twiner_url . '?type=2&cohort_remove=' . $cohort->id, get_string('remove', 'local_solin_twiner')); ?></td>
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