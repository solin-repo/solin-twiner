<?php
defined('MOODLE_INTERNAL') || die();

class twiner_notify extends twiner_info
{
	static $actionname = "notify";

	public static function get_and_print_action_triggers()
	{
		global $DB;
		global $twiner_url;

		$triggers = self::get_triggers(self::$actionname);

		if (count($triggers > 0))
		{
			echo "<h2>" . get_string('notifications', 'local_solin_twiner') . "</h2>\n";
			echo "<table class=\"generaltable\">\n";
				echo "<tr>\n";
					echo "<th>" . get_string('event', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('type', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('user', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('subject', 'local_solin_twiner') . "</th>\n";
					echo "<th>&nbsp;</th>\n";
				echo "</tr>\n";
			foreach ($triggers as $trigger)
			{
				if ($trigger->target_id > 0) $user = $DB->get_record('user', array('id' => $trigger->target_id));
				echo "<tr>\n";
					echo "<td>" . call_user_func(array($trigger->eventname, 'get_name')) . "</td>\n";
					echo "<td>" . get_string($trigger->targettype, 'local_solin_twiner') . "</td>\n";
					echo "<td>" . (($trigger->target_id > 0)?fullname($user):get_string('newly_created', 'local_solin_twiner')) . "</td>\n";
					echo "<td>" . $trigger->subject . "</td>\n";
					echo "<td>" . html_writer::link($twiner_url . '?type=2&remove=' . $trigger->id, get_string('remove', 'local_solin_twiner')) . "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "<p>&nbsp;</p>\n";
		}
	}

}

?>