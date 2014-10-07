<?php
//------------------------------------------------------------------------------
// List template
//------------------------------------------------------------------------------
echo "<h4>" . get_string('list_of_triggers', 'local_solin_twiner') . "</h4>\n";


//--------------------
// Remove triggers
//--------------------
$remove = optional_param('remove', 0, PARAM_INT);
if ($remove)
{
	$DB->delete_records('twiner_triggers', array('id' => $remove));
	$DB->delete_records('twiner_trigger_info', array('trigger_id' => $remove));
	echo "<p>" . get_string('trigger_removed', 'local_solin_twiner') . "</p>\n";
}


//----------------------------------------
// Number of triggers & actions
//----------------------------------------
$number_of_triggers = $DB->count_records_sql("SELECT COUNT(id) FROM {twiner_triggers};");
$actions = $DB->get_records_sql("SELECT * FROM {twiner_events} GROUP BY action ORDER BY action");


//--------------------
// Display triggers
//--------------------
if ($number_of_triggers > 0)
{
	require_once("classes/twiner_info.php");

	foreach ($actions as $action)
	{
		require_once("classes/" . $action->action . ".php");
		eval("twiner_" . $action->action . "::get_and_print_action_triggers();");
	}

}
else
{
	echo "<p>" . get_string('no_triggers', 'local_solin_twiner') . "</p>\n";
}
?>