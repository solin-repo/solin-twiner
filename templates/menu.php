
<?=html_writer::link($twiner_url.'?type=1',get_string('create_trigger','local_solin_twiner'));?> | <?=html_writer::link($twiner_url.'admin.php?type=2',get_string('show_triggers','local_solin_twiner'));?>
<br>
<?

if($page_context==1) include('new.php');
if($page_context==2) include('list.php');
