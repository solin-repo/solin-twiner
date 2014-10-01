<?php
//------------------------------------------------------------------------------
// Create new trigger
//------------------------------------------------------------------------------
require_once('classes/form_helper.php');

global $USER;
$created = false;
$error = false;

$available_events = $DB->get_records_sql('SELECT id, eventname FROM {twiner_triggers} GROUP BY eventname');

$new_trigger = optional_param_array('trigger', array(), PARAM_RAW);
$event = (isset($new_trigger['event'])?$new_trigger['event']:0);
$action_id = (isset($new_trigger['action'])?$new_trigger['action']:0);
$targettype = (isset($new_trigger['targettype'])?$new_trigger['targettype']:'individual');
$target_id = (isset($new_trigger['target_id'])?$new_trigger['target_id']:0);

print_object($new_trigger); 

if ($event)
{
    $eventname = $DB->get_record('twiner_triggers', array('id' => $event), 'eventname');
    if (!$eventname)
    {
        $event = false;
    }
	else
    {
        $available_actions = $DB->get_records_sql('SELECT id, action FROM {twiner_triggers} WHERE eventname = ? GROUP BY action', array($eventname->eventname));
        if ($action_id && $DB->record_exists('twiner_triggers', array('id' => $action_id)))
        {
            $selected_trigger = $DB->get_record('twiner_triggers', array('id' => $action_id));
			print_object($selected_trigger);
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
					<select name="trigger[event]" onchange="<?= (($event)?"document.getElementById('trigger_action').value = 0; ": "") ?>document.twiner_form.submit();">
						<option value="0" <?= ($action_id)?'':' selected'; ?>><?= get_string('default_event', 'local_solin_twiner'); ?></option>
						<?
						foreach($available_events as $trigger)
						{
							$name = call_user_func(array($trigger->eventname, 'get_name'));
							echo '<option value="' . $trigger->id . '" ' . (($trigger->id == $event)?'selected':'') . ' >' . $name . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<? if ($event): ?>
			<tr>
				<td><?= get_string('select_action', 'local_solin_twiner'); ?>:</td>
				<td>
					<select id="trigger_action" name="trigger[action]" onchange="document.twiner_form.submit();">
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
			<? if ($event && $action_id): ?>
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