<?php
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
        }
		else
		{
            $action_id = 0;
        }
    }
}
if (isset($_REQUEST['add_trigger']) && trim($_REQUEST['add_trigger']) == 1 && isset($selected_trigger) && $target_id)
{
	if (trim($new_trigger['subject']) == "")						$error = "no_subject";
	else if (trim(strip_tags($new_trigger['message'])) == "")	$error = "no_message";

	if (!$error)
	{
		$current_user = get_current_user();
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
?>

<h4><?= get_string('create_new_trigger', 'local_solin_twiner'); ?></h4>
<? if (!$created): ?>
<?php if ($error != false) echo "<p style=\"color: red;\">" . get_string($error, 'local_solin_twiner') . "</p>\n"; ?>
<form action="<?= $_SERVER['PHP_SELF'] ?>?type=1" name="twiner_form" method="post">
    <table>
        <tr>
            <td width="120"><?= get_string('select_event', 'local_solin_twiner'); ?>:</td>
            <td>
                <select name="trigger[event]" onchange="document.twiner_form.submit()">
                    <option value="0" disabled="disabled" <?= ($action_id)?'':'selected'; ?> ><?= get_string('nothing', 'local_solin_twiner', strtolower(get_string('event', 'local_solin_twiner'))); ?></option>
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
                <select name="trigger[action]" onchange="document.twiner_form.submit()">
                    <option value="0" disabled="disabled" <?= ($action_id)?'':'selected'; ?> ><?= get_string('nothing','local_solin_twiner', get_string('action', 'local_solin_twiner')); ?></option>
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
				<?= local_solin_twiner_form_helper::give_targettype_table_row($selected_trigger->action); ?>
				<?= local_solin_twiner_form_helper::give_target_id_options_table_row($targettype); ?>
				<tr>
					<td>
						<?= get_string('subject', 'local_solin_twiner'); ?>:
					</td>
					<td>
						<input name="trigger[subject]" value="<?= ((isset($new_trigger['subject']))?$new_trigger['subject']:"") ?>" style="width: 410px;" />
					</td>
				</tr>
				<tr>
					<td colspan="2" height="5"></td>
				</tr>
				<tr>
					<td valign="top">
						<?= get_string('message', 'local_solin_twiner'); ?>:
					</td>
					<td>
						<textarea name="trigger[message]" rows="5" style="width: 400px;"><?= ((isset($new_trigger['message']))?$new_trigger['message']:"") ?></textarea>
					</td>
				</tr>
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