<?
$available_events = $DB->get_records_sql('SELECT id,eventname FROM {twiner_triggers} GROUP BY eventname');
$trigger_base_id = optional_param('trigger_id',-1,PARAM_INT);

?>

<h4><?=get_string('create_new_trigger', 'local_solin_twiner');?></h4>

<form action='' method="POST">
    <table>
        <tr>
            <td><?=get_string('select_event','local_solin_twiner');?>:</td>
            <td>
                <select name="trigger[event]" onchange="window.location = '<?=$twiner_url;?>?type=1&trigger_id=' + $(this).val()">
                    <?
                    foreach($available_events as $trigger)
                    {
                        eval('$name = '.$trigger->eventname.'::get_name();');
                        echo '<option value="'.$trigger->id.'">'.$name.'</option>';
                    }

                    ?>
                </select>
            </td>
        </tr>
        <? if($trigger_id):?>
        <tr>
            <td><?=get_string('select_action','local_solin_twiner');?>:</td>
            <td>
                <select name="trigger[action]" onchange="window.location = '<?=$twiner_url;?>?type=1&trigger_id=<?=$trigger_id;?>' + $(this).val()">
                    <?
                    foreach($available_events as $trigger)
                    {
                        eval('$name = '.$trigger->eventname.'::get_name();');
                        echo '<option value="'.$trigger->id.'">'.$name.'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <? endif;?>
        <tr>
            <td colspan="2">
                <input type="submit" name="create" value="" class="btn"/>
            </td>
        </tr>
    </table>
</form>
