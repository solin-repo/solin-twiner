<?php
defined('MOODLE_INTERNAL') || die();

class local_solin_twiner_form_helper 
{

	public static function give_targettype_table_row($trigger) 
	{
		global $targettype;

		$targettype_string = "";

		if ($trigger == 'notify')
		{
			$targettype_string .= "<tr>\n";
			$targettype_string .= "<td>" . get_string('select_targettype', 'local_solin_twiner') . ":</td>\n";
			$targettype_string .= "<td>\n";
			$targettype_string .= "<select name=\"trigger[targettype]\" onchange=\"document.twiner_form.submit();\">\n";
			$targettype_string .= "<option value=\"individual\"" . (($targettype == strtolower(get_string('individual', 'local_solin_twiner')))?" selected":"") . ">" . get_string('individual', 'local_solin_twiner') . "</option>\n";
			$targettype_string .= "<option value=\"course\"" . (($targettype == strtolower(get_string('course', 'local_solin_twiner')))?" selected":"") . ">" . get_string('course', 'local_solin_twiner') . "</option>\n";
			$targettype_string .= "</select>\n";
			$targettype_string .= "</td>\n";
			$targettype_string .= "</tr>\n";               
		}

		return $targettype_string;
    }

	
	public static function give_target_id_options_table_row($targettype)
	{
		global $DB, $USER, $target_id;


		$target_id_string  = "<tr>\n";
		$target_id_string .= "<td>" . get_string('select_user', 'local_solin_twiner') . ":</td>\n";
		$target_id_string .= "<td>\n";
		$target_id_string .= "<select name=\"trigger[target_id]\" onchange=\"document.twiner_form.submit();\">\n";
		
		if ($targettype == 'individual')
		{
			$users = $DB->get_records('user', array('confirmed' => 1, 'deleted' => 0, 'suspended' => 0));
			$admin = get_admin();
			unset($users[$admin->id]);
			if ($USER->id != $admin->id) unset($users[$USER->id]);

			$target_id_string .= "<option value=\"" . $USER->id . "\" >" . get_string('self', 'local_solin_twiner') . "</option>\n";
			if ($USER->id != $admin->id) $target_id_string .= "<option value=\"" . $admin->id . "\"" . (($admin->id == $target_id)?' selected':'') . ">" . get_string('admin') . "</option>\n";
			foreach($users as $user)
			{
				$target_id_string .= "<option value=\"" . $user->id . "\"" . (($user->id == $target_id)?' selected':'') . ">" . $user->firstname . " " . $user->lastname . "</option>\n";
			}
		}
		else if ($targettype == 'course')
		{
			$courses = $DB->get_records_sql('SELECT id, fullname, shortname FROM {course} WHERE id > 1');
			foreach($courses as $course)
			{
				$target_id_string .= "<option value=\"" . $course->id . "\"" . (($course->id == $target_id)?' selected':'') . ">" . $course->fullname . "</option>\n";
			}
		}

		$target_id_string .= "</select>\n";
		$target_id_string .= "</td>\n";
		$target_id_string .= "</tr>\n";

		return $target_id_string;
	}

}