<?php
$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => 'local_solintwiner_observer::check_user_defined_handlers',
        'includefile' => 'local/solin-twiner/classes/observer.php',
        'priority'    => 9999
    ),
    array(
        'eventname'   => 'core\event\course_category_created',
        'callback'    => 'local_solintwiner_observer::check_user_defined_handlers',
        'includefile' => 'local/solin-twiner/classes/observer.php',
        'priority'    => 9999
    ),
    array(
        'eventname'   => 'core\event\course_created',
        'callback'    => 'local_solintwiner_observer::check_user_defined_handlers',
        'includefile' => 'local/solin-twiner/classes/observer.php',
        'priority'    => 9999
    )

);


