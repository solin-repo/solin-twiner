<?php
$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => 'local_solin_twiner_observer::check_user_defined_handlers',
        'includefile' => 'local/solin_twiner/classes/observer.php',
        'priority'    => 9999
    )
);


