<?php
if (is_siteadmin())
{
	$remove_event = optional_param('remove_event', -1, PARAM_INT);
	$add_event = optional_param('add_event', -1, PARAM_INT);
	$event_created = false;

	if ($remove_event > 0)
	{
		$DB->delete_records('twiner_triggers', array('id' => $remove_event));
		echo "<p>" . get_string('event_removed', 'local_solin_twiner') . "</p>\n";
	}
	
	if ($add_event > 0)
	{
		$eventname = optional_param('eventname', 0, PARAM_RAW);
		$actionname = optional_param('actionname', 0, PARAM_RAW);
		
		if ($eventname !== 0 && $actionname !== 0)
		{
			$record = new stdClass();
			$record->eventname = $eventname;
			$record->action = $actionname;

			if ($DB->insert_record('twiner_triggers', $record)) $event_created = true;
		}
	}

	$current_triggers = $DB->get_records('twiner_triggers');

	if (count($current_triggers) > 0)
	{
		?>
		<h4><?= get_string('current_events', 'local_solin_twiner'); ?></h4>
		<?php if ($event_created) echo "<p>" . get_string('event_created', 'local_solin_twiner') . "</p>\n"; ?>
		<table class="generaltable">
		<tr>
			<th><?= get_string('eventname', 'local_solin_twiner'); ?></th>
			<th><?= get_string('action', 'local_solin_twiner'); ?></th>
			<th>&nbsp;</th>
		</tr>
		<?php
		foreach($current_triggers as $current_trigger)
		{
			echo "<tr>\n";
				echo "<td>" . $current_trigger->eventname . "</td>\n";
				echo "<td>" . $current_trigger->action . "</td>\n";
				echo "<td>" . html_writer::link($twiner_url . '?type=3&remove_event=' . $current_trigger->id, get_string('remove', 'local_solin_twiner')) . "</td>\n";
			echo "</tr>\n";
		}
		?>
		</table>
		<br />
		<?php
	}

	$all_events = report_eventlist_list_generator::get_all_events_list();
	?>

	<h4><?= get_string('define_new_event', 'local_solin_twiner'); ?></h4>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" name="twiner_form" method="post">
	<table>
	<tr>
		<td valign="top" width="100"><?= get_string('eventname', 'local_solin_twiner'); ?>:</td>
		<td>
			<select name="eventname" onchange="checkSubmit();">
				<option value="0"><?= get_string('default_eventname', 'local_solin_twiner') ?></option>
				<?php
				foreach($all_events as $tmp_event)
				{
					echo "<option value=\"" . $tmp_event['eventname'] . "\">" . $tmp_event['eventname'] . "</option>\n";
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top"><?= get_string('actionname', 'local_solin_twiner'); ?>:</td>
		<td>
			<select name="actionname" onchange="checkSubmit();">
				<option value="0"><?= get_string('default_actionname', 'local_solin_twiner') ?></option>
				<option value="notify"><?= get_string('notify_option', 'local_solin_twiner') ?></option>
				<option value="enrol"><?= get_string('enrol_option', 'local_solin_twiner') ?></option>
				<option value="group"><?= get_string('group_option', 'local_solin_twiner') ?></option>
				<option value="cohort"><?= get_string('cohort_option', 'local_solin_twiner') ?></option>
			</select>
		</td>
	</tr>
	<tr height="50">
		<td valign="top" colspan="2"><input id="createButton" type="button" name="create" value="<?= get_string('submit'); ?>" class="btn" onclick="document.twiner_form.submit();" style="display: none;" /></td>
	</table>
	<input type="hidden" name="type" value="3" />
	<input type="hidden" name="add_event" value="1" />
	</form>
	<script type="text/javascript">
	function checkSubmit()
	{
		if (document.twiner_form.eventname.value == 0 || document.twiner_form.actionname.value == 0)	document.getElementById('createButton').style.display = "none";
		else																							document.getElementById('createButton').style.display = "block";
	}
	</script>
	<?php
}
else
{
	echo "<p>" . get_string('define_event_only_admin', 'local_solin_twiner') . "</p>\n";
}
?>