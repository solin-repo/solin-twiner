<?php

class twiner_info
{
	public static function get_triggers($actionname)
	{
		global $DB;
		
		$sql  = "SELECT tt.*, te.eventname, te.action ";
		$sql .= "FROM {twiner_triggers} tt ";
		$sql .= "JOIN {twiner_events} te ON tt.event_id = te.id ";
		$sql .= "WHERE te.action = '%s'";

		$triggers = $DB->get_records_sql(sprintf($sql, $actionname));

		// Get additional info
		if (count($triggers > 0))
		{
			foreach ($triggers as $trigger)
			{
				if (count($additional_info = $DB->get_records('twiner_trigger_info', array('trigger_id' => $trigger->id))) > 0)
				{ 
					foreach ($additional_info as $info)
					{
						$tmp_name = $info->name;
						$trigger->$tmp_name = $info->value;
					}
				}
			}
		}

		return $triggers;
	}

}

?>