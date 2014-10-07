<?php
echo "<h1>" . get_string('pluginname', 'local_solin_twiner') . "</h1>\n";
echo html_writer::link($twiner_url . '?type=1', get_string('create_trigger', 'local_solin_twiner'));
echo " | ";
echo html_writer::link($twiner_url . '?type=2', get_string('show_triggers', 'local_solin_twiner'));
if (is_siteadmin()) echo " | " . html_writer::link($twiner_url . '?type=3', get_string('define_event','local_solin_twiner'));
echo "<hr>\n";
?>