<?php
defined('MOODLE_INTERNAL') || die();

class twiner_group extends twiner_info
{
	static $actionname = "group";

	public static function get_and_print_action_triggers()
	{
		global $DB;
		global $twiner_url;

		$triggers = self::get_triggers(self::$actionname);

		if (count($triggers > 0))
		{
			echo "<h2>" . get_string('groups', 'local_solin_twiner') . "</h2>\n";
			echo "<table class=\"generaltable\">\n";
				echo "<tr>\n";
					echo "<th>" . get_string('event', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('user', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('course', 'local_solin_twiner') . "</th>\n";
					echo "<th>" . get_string('group_header', 'local_solin_twiner') . "</th>\n";
					echo "<th>&nbsp;</th>\n";
				echo "</tr>\n";
			foreach ($triggers as $trigger)
			{
				if ($trigger->target_id > 0) $user = $DB->get_record('user', array('id' => $trigger->target_id));
				if ($trigger->course_id > 0) $course = $DB->get_record('course', array('id' => $trigger->course_id), "id, fullname");
				if ($trigger->group_id > 0) $group = $DB->get_record('groups', array('id' => $trigger->group_id), "id, name");
				echo "<tr>\n";
					echo "<td>" . call_user_func(array($trigger->eventname, 'get_name')) . "</td>\n";
					echo "<td>" . (($trigger->target_id > 0)?fullname($user):get_string('newly_created', 'local_solin_twiner')) . "</td>\n";
					echo "<td>" . $course->fullname . "</td>\n";
					echo "<td>" . $group->name . "</td>\n";
					echo "<td>" . html_writer::link($twiner_url . '?type=2&remove=' . $trigger->id, get_string('remove', 'local_solin_twiner')) . "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "<p>&nbsp;</p>\n";
		}
	}
	
}

?>