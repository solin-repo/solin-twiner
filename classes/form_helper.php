<?php
defined('MOODLE_INTERNAL') || die();

class local_solin_twiner_form_helper 
{
	public static function give_table_rows()
	{
		global $new_trigger;

		$table_rows = "";
		$table_rows .= self::give_targettype_table_row();
		$table_rows .= self::give_target_id_options_table_row();
		$table_rows .= self::give_subject_and_message_table_rows();
		$table_rows .= self::give_course_table_row();
		$table_rows .= self::give_cohort_table_row();
		if (isset($new_trigger['course_id'])) $table_rows .= self::give_groups_table_row();

		return $table_rows;

	}


	public static function give_targettype_table_row() 
	{
		global $selected_trigger, $targettype;

		$tablerow = "";

		if ($selected_trigger->action == 'notify')
		{
			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('select_targettype', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td>\n";
			$tablerow .= "<select name=\"trigger[targettype]\" onchange=\"document.twiner_form.submit();\">\n";
			$tablerow .= "<option value=\"individual\"" . (($targettype == strtolower(get_string('individual', 'local_solin_twiner')))?" selected":"") . ">" . get_string('individual', 'local_solin_twiner') . "</option>\n";
			$tablerow .= "<option value=\"course\"" . (($targettype == strtolower(get_string('course', 'local_solin_twiner')))?" selected":"") . ">" . get_string('course', 'local_solin_twiner') . "</option>\n";
			$tablerow .= "</select>\n";
			$tablerow .= "</td>\n";
			$tablerow .= "</tr>\n";               
		}

		return $tablerow;
    }
	

	public static function give_target_id_options_table_row()
	{
		global $DB, $USER, $selected_trigger, $targettype, $target_id;
		
		$targettype_string = "select_user";
		if ($targettype == 'course') $targettype_string = "select_course";
		$tablerow  = "<tr>\n";
		$tablerow .= "<td>" . get_string($targettype_string, 'local_solin_twiner') . ":</td>\n";
		$tablerow .= "<td>\n";
		$tablerow .= "<select name=\"target_id\">\n";
		
		if ($targettype == 'individual')
		{
			$users = $DB->get_records('user', array('confirmed' => 1, 'deleted' => 0, 'suspended' => 0));
			$admin = get_admin();
			unset($users[$admin->id]);
			if ($USER->id != $admin->id) unset($users[$USER->id]);
			// Fixme - students only to self
			// Fixme - when newly created / updated, or just the user it's about
			//if ($selected_trigger->eventname == '\core\event\user_created' || $selected_trigger->eventname == '\core\event\user_updated')	
				$tablerow .= "<option value=\"-1\" >" . get_string('newly_created', 'local_solin_twiner') . "</option>\n";
			$tablerow .= "<option value=\"" . $USER->id . "\" >" . get_string('self', 'local_solin_twiner') . "</option>\n";
			if ($USER->id != $admin->id) $tablerow .= "<option value=\"" . $admin->id . "\"" . (($admin->id == $target_id)?' selected':'') . ">" . get_string('admin') . "</option>\n";
			foreach($users as $user)
			{
				$tablerow .= "<option value=\"" . $user->id . "\"" . (($user->id == $target_id)?' selected':'') . ">" . $user->firstname . " " . $user->lastname . "</option>\n";
			}
		}
		else if ($targettype == 'course')
		{
			$courses = $DB->get_records_sql('SELECT id, fullname, shortname FROM {course} WHERE id > 1');
			foreach($courses as $course)
			{
				$tablerow .= "<option value=\"" . $course->id . "\"" . (($course->id == $target_id)?' selected':'') . ">" . $course->fullname . "</option>\n";
			}
		}

		$tablerow .= "</select>\n";
		$tablerow .= "</td>\n";
		$tablerow .= "</tr>\n";

		return $tablerow;
	}

	
	public static function give_subject_and_message_table_rows()
	{
		global $selected_trigger, $new_trigger;
		
		$tablerow = "";

		if ($selected_trigger->action == 'notify')
		{
			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('subject', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td><input name=\"trigger[subject]\" value=\"" . ((isset($new_trigger['subject']))?$new_trigger['subject']:"") . "\" style=\"width: 410px;\" /></td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "<tr>\n";
			$tablerow .= "<td colspan=\"2\" height=\"5\"></td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "<tr>\n";
			$tablerow .= "<td valign=\"top\">" . get_string('message', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td><textarea name=\"trigger[message]\" rows=\"5\" style=\"width: 400px;\">" . ((isset($new_trigger['message']))?$new_trigger['message']:"") . "</textarea></td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "\n";					
		}

		return $tablerow;
	}


	public static function give_course_table_row()
	{
		global $selected_trigger, $new_trigger, $DB;
		
		$tablerow = "";

		if ($selected_trigger->action == 'enrol' || $selected_trigger->action == 'group')
		{
			$courses = $DB->get_records_sql('SELECT id, fullname, shortname FROM {course} WHERE id > 1');

			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('select_course', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td>\n";
			$tablerow .= "<select name=\"trigger[course_id]\"" . ($selected_trigger->action == 'group'?" onchange=\"document.twiner_form.submit();":"")  . "\">\n";
			foreach($courses as $course)
			{
				$tablerow .= "<option value=\"" . $course->id . "\"" . ((isset($new_trigger['course_id']) && $new_trigger['course_id'] == $course->id)?' selected':'') . ">" . $course->fullname . "</option>\n";
			}
			$tablerow .= "</select>\n";
			$tablerow .= "</td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "<tr>\n";
		}

		return $tablerow;
	}


	public static function give_groups_table_row()
	{
		global $selected_trigger, $new_trigger, $DB;

		$tablerow = "";

		if ($selected_trigger->action == 'group')
		{
			$groups = $DB->get_records_sql('SELECT id, name FROM {groups} WHERE courseid =' . $new_trigger['course_id']);

			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('select_group', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td>\n";
			$tablerow .= "<select name=\"trigger[group_id]\">\n";
			foreach($groups as $group)
			{
				$tablerow .= "<option value=\"" . $group->id . "\"" . ((isset($new_trigger['group_id']) && $new_trigger['group_id'] == $group->id)?' selected':'') . ">" . $group->name . "</option>\n";
			}
			$tablerow .= "</select>\n";
			$tablerow .= "</td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "<tr>\n";
		}

		return $tablerow;
	}


	public static function give_cohort_table_row()
	{
		global $selected_trigger, $new_trigger, $DB;

		$tablerow = "";

		if ($selected_trigger->action == 'cohort')
		{
			$cohorts = $DB->get_records_sql('SELECT id, name FROM {cohort}');

			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('select_cohort', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td>\n";
			$tablerow .= "<select name=\"trigger[cohort_id]\">\n";
			foreach($cohorts as $cohort)
			{
				$tablerow .= "<option value=\"" . $cohort->id . "\"" . ((isset($new_trigger['cohort_id']) && $new_trigger['cohort_id'] == $cohort->id)?' selected':'') . ">" . $cohort->name . "</option>\n";
			}
			$tablerow .= "</select>\n";
			$tablerow .= "</td>\n";
			$tablerow .= "</tr>\n";
			$tablerow .= "<tr>\n";
		}

		return $tablerow;
	}

}
?>