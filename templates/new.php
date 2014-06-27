<?
global $USER;
$created = false;
$available_events = $DB->get_records_sql('SELECT id,eventname FROM {twiner_triggers} GROUP BY eventname');
$event = optional_param('event',0,PARAM_INT);
$action_id = optional_param('action_id',0,PARAM_INT);
$target_id = optional_param('target_id',0,PARAM_INT);
$new_trigger = optional_param_array('trigger',array(),PARAM_ALPHANUMEXT);

if($event)
{
    $eventname = $DB->get_record('twiner_triggers',array('id'=>$event),'eventname');
    if(!$eventname)
    {
        $event = false;
    }else
    {
        $available_actions = $DB->get_records_sql('SELECT id,action FROM {twiner_triggers} WHERE eventname=? GROUP BY action',array($eventname->eventname));
        if($action_id&&$DB->record_exists('twiner_triggers',array('id'=>$action_id)))
        {
            $selected_trigger = $DB->get_record('twiner_triggers',array('id'=>$action_id));
            $users = $DB->get_records('user',array('confirmed'=>1,'deleted'=>0,'suspended'=>0));
            $admin = get_admin();
            unset($users[$admin->id]);
        }else{
            $action_id = 0;
        }
    }
}
if(isset($new_trigger['add'])&&$new_trigger['add']==1&&isset($selected_trigger)&&$target_id)
{
    $current_user = get_current_user();
    $insert = new stdClass();
    $insert->creator_id = $USER->id;
    $insert->type = 'individual';
    $insert->trigger_id = $selected_trigger->id;
    $insert->target_id = $target_id;
    $insert->subject = $new_trigger['subject'];
    $insert->body = $new_trigger['message'];
    if($DB->insert_record('twiner_notifications',$insert))
        $created = true;
}
?>

<h4><?=get_string('create_new_trigger', 'local_solin_twiner');?></h4>
<? if(!$created):?>
<form action='' method="POST">
    <table>
        <tr>
            <td><?=get_string('select_event','local_solin_twiner');?>:</td>
            <td>
                <select name="trigger[event]" onchange="window.location = '<?=$twiner_url;?>?type=1&event=' + $(this).val()">
                    <option value="0" disabled="disabled" <?=($action_id)?'':'selected';?> ><?=get_string('nothing','local_solin_twiner');?></option>
                    <?
                    foreach($available_events as $trigger)
                    {
                        eval('$name = '.$trigger->eventname.'::get_name();');
                        echo '<option value="'.$trigger->id.'" '.(($trigger->id==$event)?'selected':'').' >'.$name.'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <? if($event):?>
        <tr>
            <td><?=get_string('select_action','local_solin_twiner');?>:</td>
            <td>
                <select name="trigger[action]" onchange="window.location = '<?=$twiner_url;?>?type=1&event=<?=$event;?>&action_id=' + $(this).val()">
                    <option value="0" disabled="disabled" <?=($action_id)?'':'selected';?> ><?=get_string('nothing','local_solin_twiner');?></option>
                    <?
                    foreach($available_actions as $action)
                    {
                        echo '<option value="'.$action->id.'" '.(($action->id==$action_id)?'selected':'').' >'.get_string($action->action,'local_solin_twiner').'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
            <? if($action_id):?>
                <tr>
                    <td><?=get_string('select_user','local_solin_twiner');?>:</td>
                    <td>
                        <select name="trigger[target_id]" onchange="window.location = '<?=$twiner_url;?>?type=1&event=<?=$event;?>&action_id=<?=$action_id;?>&target_id=' + $(this).val()">
                            <option value="0" ><?=get_string('self','local_solin_twiner');?></option>
                            <option value="<?=$admin->id;?>" <?=($admin->id==$target_id)?'selected':'';?>><?=get_string('admin');?></option>
                            <?
                            foreach($users as $user)
                            {
                                echo '<option value="'.$user->id.'" '.(($user->id==$target_id)?'selected':'').' >'.$user->firstname.' '.$user->lastname.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <? if($target_id&&$selected_trigger->action=='notify'):?>
                    <tr>
                        <td>
                            <?=get_string('subject','local_solin_twiner');?>
                        </td>
                        <td>
                            <input name="trigger[subject]">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?=get_string('message','local_solin_twiner');?>
                        </td>
                        <td>
                            <input type="text" name="trigger[message]">
                        </td>
                    </tr>

                <? endif;?>
            <? endif;?>
        <? endif;?>
        <tr>
            <td colspan="2">
                <input type="submit" name="create" value="<?=get_string('submit');?>" class="btn"/>
            </td>
        </tr>
    </table>
    <input type="hidden" name="trigger[add]" value="1">
</form>
<? else: ?>
    <h3><?=get_string('created','local_solin_twiner');?></h3>
<? endif;?>