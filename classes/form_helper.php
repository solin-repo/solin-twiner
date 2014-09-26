<?php
defined('MOODLE_INTERNAL') || die();

class local_solin_twiner_form_helper 
{
	public static function give_table_rows()
	{	
		$table_rows = "";
		$table_rows .= self::give_targettype_table_row();
		$table_rows .= self::give_target_id_options_table_row();
		$table_rows .= self::give_subject_and_message_table_rows();
		$table_rows .= self::give_course_enrol_table_row();

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
		global $DB, $USER, $targettype, $target_id;
		
		$targettype_string = "select_user";
		if ($targettype == 'course') $targettype_string = "select_course";
		$tablerow  = "<tr>\n";
		$tablerow .= "<td>" . get_string($targettype_string, 'local_solin_twiner') . ":</td>\n";
		$tablerow .= "<td>\n";
		$tablerow .= "<select name=\"trigger[target_id]\">\n";
		
		if ($targettype == 'individual')
		{
			$users = $DB->get_records('user', array('confirmed' => 1, 'deleted' => 0, 'suspended' => 0));
			$admin = get_admin();
			unset($users[$admin->id]);
			if ($USER->id != $admin->id) unset($users[$USER->id]);

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


	public static function give_course_enrol_table_row()
	{
		global $selected_trigger, $DB;
		
		$tablerow = "";

		if ($selected_trigger->action == 'enrol')
		{
			$courses = $DB->get_records_sql('SELECT id, fullname, shortname FROM {course} WHERE id > 1');

			$tablerow .= "<tr>\n";
			$tablerow .= "<td>" . get_string('select_course', 'local_solin_twiner') . ":</td>\n";
			$tablerow .= "<td>\n";
			$tablerow .= "<select name=\"trigger[course_id]\">\n";
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

}