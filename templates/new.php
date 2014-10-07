<?php
//------------------------------------------------------------------------------
// Create new trigger
//------------------------------------------------------------------------------
require_once('classes/form_helper.php');

global $USER;
$created = false;
$error = false;

$available_events = $DB->get_records_sql('SELECT id, eventname FROM {twiner_events} GROUP BY eventname');

$new_trigger = optional_param_array('trigger', array(), PARAM_RAW);
$event_id = optional_param('event_id', 0, PARAM_RAW);
$action_id = optional_param('action_id', 0, PARAM_RAW);
$target_id = optional_param('target_id', 0, PARAM_RAW);
$targettype = (isset($new_trigger['targettype'])?$new_trigger['targettype']:'individual');

//print_object($new_trigger); 

if ($event_id)
{
    $eventname = $DB->get_record('twiner_events', array('id' => $event_id), 'eventname');
    if (!$eventname)
    {
        $event_id = false;
    }
	else
    {
        $available_actions = $DB->get_records_sql('SELECT id, action FROM {twiner_events} WHERE eventname = ? GROUP BY action', array($eventname->eventname));
        if ($action_id && $DB->record_exists('twiner_events', array('id' => $action_id)))
        {
            $selected_trigger = $DB->get_record('twiner_events', array('id' => $action_id));
			//print_object($selected_trigger);
        }
		else
		{
            $action_id = 0;
        }
    }
}

//------------------------------------------------------------------------------
// Save the new trigger
//------------------------------------------------------------------------------
if (isset($_REQUEST['add_trigger']) && trim($_REQUEST['add_trigger']) == 1 && isset($selected_trigger) && $target_id)
{
	if ($selected_trigger->action == "notify")
	{
		if (trim($new_trigger['subject']) == "")										$error = "no_subject";
		else if (trim(strip_tags($new_trigger['message'])) == "")						$error = "no_message";
	}
	else if ($selected_trigger->action == "group")
	{
		if (!isset($new_trigger['group_id']) || trim($new_trigger['group_id']) == "")	$error = "no_group";
	}
	else if ($selected_trigger->action == "cohort")
	{
		if (!isset($new_trigger['cohort_id']) || trim($new_trigger['cohort_id']) == "")	$error = "no_cohort";
	}

	if (!$error)
	{
		$record = new stdClass();
		$record->creator_id = $USER->id;
		$record->event_id = $selected_trigger->id;
		$record->target_id = $target_id;

		if ($new_trigger_id = $DB->insert_record('twiner_triggers', $record)) $created = true;

		if (count($new_trigger) > 0)
		{
			foreach ($new_trigger as $name => $value)
			{
				$additional_record = new stdClass();
				$additional_record->trigger_id = $new_trigger_id;
				$additional_record->name = $name;
				$additional_record->value = $value;

				$DB->insert_record('twiner_trigger_info', $additional_record);
			}
		}
	}

	/*
	if ($selected_trigger->action == "notify")
	{
		if (trim($new_trigger['subject']) == "")						$error = "no_subject";
		else if (trim(strip_tags($new_trigger['message'])) == "")		$error = "no_message";

		if (!$error)
		{
			$insert = new stdClass();
			$insert->creator_id = $USER->id;
			$insert->trigger_id = $selected_trigger->id;
			$insert->targettype = $targettype;
			$insert->target_id = $target_id;
			$insert->subject = $new_trigger['subject'];
			$insert->body = $new_trigger['message'];

			if ($DB->insert_record('twiner_notifications', $insert)) $created = true;
		}
	}
	else if ($selected_trigger->action == "enrol")
	{
		$insert = new stdClass();
		$insert->creator_id = $USER->id;
		$insert->trigger_id = $selected_trigger->id;
		$insert->user_id = $target_id;
		$insert->course_id = $new_trigger['course_id'];

		if ($DB->insert_record('twiner_enrol', $insert)) $created = true;
	}
	else if ($selected_trigger->action == "group")
	{
		if (isset($new_trigger['group_id']) && trim($new_trigger['group_id']) != "")
		{
			$insert = new stdClass();
			$insert->creator_id = $USER->id;
			$insert->trigger_id = $selected_trigger->id;
			$insert->user_id = $target_id;
			$insert->course_id = $new_trigger['course_id'];
			$insert->group_id = $new_trigger['group_id'];

			if ($DB->insert_record('twiner_groups', $insert)) $created = true;
		}
	}
	else if ($selected_trigger->action == "cohort")
	{
		if (isset($new_trigger['cohort_id']) && trim($new_trigger['cohort_id']) != "")
		{
			$insert = new stdClass();
			$insert->creator_id = $USER->id;
			$insert->trigger_id = $selected_trigger->id;
			$insert->user_id = $target_id;
			$insert->cohort_id = $new_trigger['cohort_id'];

			if ($DB->insert_record('twiner_cohorts', $insert)) $created = true;
		}
	}
	*/
}


//------------------------------------------------------------------------------
// New trigger form
//------------------------------------------------------------------------------
?>
<h4><?= get_string('create_new_trigger', 'local_solin_twiner'); ?></h4>
<? if (!$created): ?>
	<?php if ($error != false) echo "<p style=\"color: red;\">" . get_string($error, 'local_solin_twiner') . "</p>\n"; ?>
	<form action="<?= $_SERVER['PHP_SELF'] ?>?type=1" name="twiner_form" method="post">
		<table>
			<tr>
				<td width="120"><?= get_string('select_event', 'local_solin_twiner'); ?>:</td>
				<td>
					<select name="event_id" onchange="<?= (($event_id)?"document.getElementById('trigger_action').value = 0; ": "") ?>document.twiner_form.submit();">
						<option value="0" <?= ($action_id)?'':' selected'; ?>><?= get_string('default_event', 'local_solin_twiner'); ?></option>
						<?
						foreach($available_events as $available_event)
						{
							$name = call_user_func(array($available_event->eventname, 'get_name'));
							echo '<option value="' . $available_event->id . '" ' . (($available_event->id == $event_id)?'selected':'') . ' >' . $name . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<? if ($event_id): ?>
			<tr>
				<td><?= get_string('select_action', 'local_solin_twiner'); ?>:</td>
				<td>
					<select id="trigger_action" name="action_id" onchange="document.twiner_form.submit();">
						<option value="0" <?= ($action_id)?'':' selected'; ?>><?= get_string('default_action','local_solin_twiner'); ?></option>
						<?
						foreach($available_actions as $action)
						{
							echo '<option value="' . $action->id . '" ' . (($action->id == $action_id)?'selected':'') . ' >' . get_string($action->action, 'local_solin_twiner') . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
				<? if ($action_id): ?>
					<?= local_solin_twiner_form_helper::give_table_rows(); ?>
				<? endif; ?>
			<? endif; ?>
			<? if ($event_id && $action_id): ?>
			<tr>
				<td colspan="2">
					<input type="button" name="create" value="<?= get_string('submit'); ?>" class="btn" onclick="document.twiner_form.add_trigger.value = 1;document.twiner_form.submit();" />
				</td>
			</tr>
			<? endif; ?>
		</table>
		<input type="hidden" name="add_trigger" value="0" />
	</form>
<? else: ?>
    <h4><?= get_string('created', 'local_solin_twiner'); ?></h4>
	<p><?= html_writer::link($twiner_url . '?type=2', get_string('show_triggers', 'local_solin_twiner')); ?></p>
<? endif; ?>